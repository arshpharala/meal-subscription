<?php

namespace App\Services;

use App\Models\Sales\Subscription;
use App\Models\Sales\SubscriptionRenewalLog;
use App\Services\StripeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoChargeService
{
    protected StripeService $stripe;

    public function __construct(StripeService $stripe)
    {
        $this->stripe = $stripe;
    }

    /**
     * Process all subscriptions that are due for auto charge.
     */
    public function chargeDueSubscriptions(): void
    {
        $today = now()->startOfDay();

        $dueSubscriptions = Subscription::query()
            ->where('auto_charge', true)
            ->where('status', 'active')
            ->whereDate('next_charge_date', '<=', $today)
            ->with(['user', 'mealPackagePrice.calorie', 'address.country.tax'])
            ->get();

        if ($dueSubscriptions->isEmpty()) {
            Log::info('[AutoCharge] No subscriptions due for renewal.');
            return;
        }

        Log::info('[AutoCharge] Found ' . $dueSubscriptions->count() . ' subscriptions due for renewal.');

        foreach ($dueSubscriptions as $subscription) {
            try {
                $this->attemptCharge($subscription);
            } catch (\Throwable $e) {
                Log::error('[AutoCharge] Error for Subscription ' . $subscription->id . ': ' . $e->getMessage());
            }
        }
    }

    /**
     * Attempt to charge a single subscription.
     */
    public function attemptCharge(Subscription $subscription): void
    {
        DB::transaction(function () use ($subscription) {
            $user = $subscription->user;
            $price = $subscription->mealPackagePrice;
            $country = $subscription->address->country ?? null;
            $tax = $country?->tax;

            $baseAmount = $price->price;
            $taxAmount = $tax ? round($baseAmount * ($tax->percentage / 100), 2) : 0;
            $totalAmount = $baseAmount + $taxAmount;

            $gatewayId = 1; // 1 = Stripe, can extend later for other gateways

            // 1️⃣ Attempt payment (currently only Stripe supported)
            $reference = null;
            $status = 'pending';
            $message = null;

            try {
                $result = $this->stripe->chargeSubscriptionRenewal($subscription, $baseAmount, $tax);
                $reference = $result->intent->id ?? null;
                $receiptUrl = $result->receipt_url ?? null;
                $status = 'success';
            } catch (\Stripe\Exception\CardException $e) {
                $status = 'failed';
                $receiptUrl = null;
                $message = $e->getError()->message ?? 'Card declined';
            } catch (\Exception $e) {
                $status = 'failed';
                $receiptUrl = null;
                $message = $e->getMessage();
            }

            SubscriptionRenewalLog::create([
                'subscription_id' => $subscription->id,
                'reference'       => $reference,
                'receipt_url'     => $receiptUrl, // ✅ store here
                'gateway_id'      => $gatewayId,
                'tax_id'          => $tax?->id,
                'currency_id'     => $country?->currency_id ?? 1,
                'amount'          => $baseAmount,
                'tax_amount'      => $taxAmount,
                'total_amount'    => $totalAmount,
                'status'          => $status,
                'message'         => $message,
                'charged_at'      => now(),
            ]);

            // 3️⃣ Update subscription if success
            if ($status === 'success') {
                $this->renewCycle($subscription);
            } else {
                $subscription->update(['status' => 'payment_failed']);
            }

            Log::info("[AutoCharge] Renewal for Sub #{$subscription->id} ({$status}) ref={$reference}");
        });
    }

    /**
     * Renew the subscription cycle (start, end, next_charge).
     */
    public function renewCycle(Subscription $subscription): void
    {
        $duration = $subscription->mealPackagePrice->duration ?? 0;
        if ($duration <= 0) {
            Log::warning("[AutoCharge] Invalid duration for Subscription {$subscription->id}");
            return;
        }

        $newStart = Carbon::parse($subscription->end_date)->addDay();
        $newEnd = $newStart->copy()->addDays($duration - 1);

        $subscription->update([
            'start_date' => $newStart,
            'end_date' => $newEnd,
            'next_charge_date' => $newEnd,
            'status' => 'active',
        ]);
    }
}
