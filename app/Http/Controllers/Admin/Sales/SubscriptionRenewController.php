<?php

namespace App\Http\Controllers\Admin\Sales;

use Illuminate\Http\Request;
use App\Models\Sales\Subscription;
use App\Services\AutoChargeService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class SubscriptionRenewController extends Controller
{

    /**
     * Retry a failed renewal charge.
     */
    public function retryRenewal(Request $request, Subscription $subscription, $renewalId)
    {
        $renewal = $subscription->renewalLogs()->findOrFail($renewalId);

        if ($renewal->status !== 'failed') {
            return response()->json([
                'success' => false,
                'title'   => 'Invalid Action',
                'message' => 'Only failed renewals can be retried.',
            ], 422);
        }

        try {
            $result = app(AutoChargeService::class)->attemptCharge($subscription);
            if ($result['status'] === 'success') {
                $renewal->update([
                    'status'       => 'success',
                    'reference'    => $result['reference'] ?? $renewal->reference,
                    'receipt_url'  => $result['receipt_url'] ?? null,
                    'message'      => 'Manual retry successful',
                    'charged_at'   => now(),
                ]);
            }

            return response()->json([
                'success' => true,
                'title'   => 'Retry Completed',
                'message' => 'The renewal has been reattempted successfully.',
                'redirect' => route('admin.sales.subscriptions.show', $subscription->id),
            ]);
        } catch (\Exception $e) {
            Log::error('Retry Charge Failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'title'   => 'Retry Failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function manualRenew(Request $request, Subscription $subscription)
    {
        try {

            if (!$this->canRenew($subscription)) {
                return response()->json([
                    'success' => false,
                    'title'   => 'Already Active',
                    'message' => 'Subscription is already active and not yet expired.',
                ]);
            }


            $result = $this->attemptManualCharge($subscription);


            $this->logRenewal($subscription, $result);


            $this->extendSubscriptionPeriod($subscription);

            return response()->json([
                'success'  => true,
                'title'    => 'Renewed Successfully',
                'message'  => 'Subscription renewed successfully.',
                'redirect' => route('admin.sales.subscriptions.show', $subscription->id)
            ]);
        } catch (\Throwable $e) {
            Log::error('Manual Renew Failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'title'   => 'Renew Failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    protected function canRenew(Subscription $subscription): bool
    {
        if ($subscription->end_date && $subscription->end_date->isFuture()) {
            return false;
        }

        if ($subscription->status === 'cancelled') {
            return false;
        }

        return true;
    }

    protected function attemptManualCharge(Subscription $subscription): array
    {
        $price = $subscription->mealPackagePrice->price;
        $charge = app(AutoChargeService::class)->attemptCharge($subscription);

        return [
            'reference'    => $charge['reference'] ?? null,
            'status'       => $charge['status'] ?? 'pending',
            'tax_amount'   => $charge['tax_amount'] ?? 0,
            'total'        => $charge['total'] ?? $price,
            'amount'       => $price,
        ];
    }

    protected function logRenewal(Subscription $subscription, array $result): void
    {
        $subscription->renewalLogs()->create([
            'gateway_id'   => 1, // Stripe default
            'reference'    => $result['reference'],
            'amount'       => $result['amount'],
            'tax_amount'   => $result['tax_amount'],
            'total_amount' => $result['total'],
            'currency_id'  => 1, // AED (make dynamic later)
            'status'       => $result['status'],
            'message'      => 'Manual renewal by admin',
            'charged_at'   => now(),
        ]);
    }

    protected function extendSubscriptionPeriod(Subscription $subscription): void
    {
        $durationDays = $subscription->mealPackagePrice->duration;

        // ✅ New start date is 1 day after old end date (avoid overlap)
        $newStartDate = $subscription->end_date
            ? $subscription->end_date->copy()->addDay()
            : now()->startOfDay();

        $newEndDate = $newStartDate->copy()->addDays($durationDays - 1);

        $subscription->update([
            'start_date'       => $newStartDate,
            'end_date'         => $newEndDate,
            'next_charge_date' => $newEndDate,
            'status'           => 'active',
        ]);
    }
}
