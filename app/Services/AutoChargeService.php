<?php

namespace App\Services;

use App\Models\Sales\Subscription;
use App\Models\Sales\SubscriptionRenewalLog;
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

    /* ============================================================
     | 1️⃣ AUTO PROCESSOR (CRON)
     ============================================================ */
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
                Log::error("[AutoCharge] Error for Sub #{$subscription->id}: " . $e->getMessage());
            }
        }
    }

    /* ============================================================
     | 2️⃣ ATTEMPT CHARGE
     ============================================================ */
    public function attemptCharge(Subscription $subscription): array
    {
        DB::beginTransaction();

        try {
            // Basic details
            $price      = $subscription->mealPackagePrice;
            $country    = $subscription->address->country ?? null;
            $tax        = $country?->tax;
            $baseAmount = $price->price;
            $taxAmount  = $tax ? round($baseAmount * ($tax->percentage / 100), 2) : 0;
            $totalAmount = $baseAmount + $taxAmount;

            // Attempt Stripe charge
            $stripeResult = $this->stripe->chargeSubscriptionRenewal($subscription);

            // Handle Stripe response
            $isSuccess = $stripeResult->status === 'success';
            $reference = $stripeResult->reference ?? null;
            $receiptUrl = $stripeResult->receipt_url ?? null;
            $message = $stripeResult->message ?? ($isSuccess ? 'Payment successful' : 'Charge failed');

            // Log renewal result
            $this->logRenewal($subscription, [
                'gateway_id'   => 1, // Stripe default
                'reference'    => $reference,
                'receipt_url'  => $receiptUrl,
                'tax_id'       => $tax?->id,
                'currency_id'  => $country?->currency_id ?? 1,
                'amount'       => $baseAmount,
                'tax_amount'   => $taxAmount,
                'total_amount' => $totalAmount,
                'status'       => $isSuccess ? 'success' : 'failed',
                'message'      => $message,
            ]);

            // Handle subscription update
            if ($isSuccess) {
                $this->renewCycle($subscription);
            } else {
                $subscription->update(['status' => 'payment_failed']);
            }

            DB::commit();

            Log::info("[AutoCharge] Subscription #{$subscription->id} charged ({$stripeResult->status}) Ref: {$reference}");

            return [
                'status'       => $stripeResult->status,
                'reference'    => $reference,
                'receipt_url'  => $receiptUrl,
                'tax_amount'   => $stripeResult->tax_amount ?? $taxAmount,
                'total'        => $stripeResult->total ?? $totalAmount,
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("[AutoCharge] Fatal error for Sub #{$subscription->id}: " . $e->getMessage());
            throw $e;
        }
    }

    /* ============================================================
     | 3️⃣ RENEWAL LOG CREATION
     ============================================================ */
    protected function logRenewal(Subscription $subscription, array $data): void
    {
        // Prevent duplicate logs for the same reference
        if (
            !empty($data['reference']) &&
            SubscriptionRenewalLog::where('subscription_id', $subscription->id)
            ->where('reference', $data['reference'])
            ->exists()
        ) {
            Log::warning("[AutoCharge] Skipped duplicate log for Ref {$data['reference']} (Sub {$subscription->id})");
            return;
        }

        $subscription->renewalLogs()->create(array_merge($data, [
            'charged_at' => now(),
        ]));
    }

    /* ============================================================
     | 4️⃣ RENEW SUBSCRIPTION CYCLE
     ============================================================ */
    public function renewCycle(Subscription $subscription): void
    {
        $duration = $subscription->mealPackagePrice->duration ?? 0;
        if ($duration <= 0) {
            Log::warning("[AutoCharge] Invalid duration for Subscription {$subscription->id}");
            return;
        }

        $newStart = Carbon::parse($subscription->end_date)->addDay();
        $newEnd   = $newStart->copy()->addDays($duration - 1);

        $subscription->update([
            'start_date'       => $newStart,
            'end_date'         => $newEnd,
            'next_charge_date' => $newEnd,
            'status'           => 'active',
        ]);
    }
}
