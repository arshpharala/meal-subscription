<?php

namespace App\Services;

use App\Models\Sales\Subscription;
use App\Models\Sales\SubscriptionFreeze;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SubscriptionScheduler
{
    /**
     * Inclusive day count between start & end.
     */
    public function computeFrozenDays(Carbon $start, Carbon $end): int
    {
        return $start->diffInDays($end) + 1;
    }

    /**
     * Baseline end date = start_date + (duration - 1)
     * (Duration comes from the linked mealPackagePrice at creation time.)
     */
    public function getBaselineEndDate(Subscription $subscription): ?Carbon
    {
        if (!$subscription->start_date) {
            return null;
        }
        $duration = (int) ($subscription->mealPackagePrice?->duration ?? 0);
        if ($duration <= 0) {
            return null;
        }
        return Carbon::parse($subscription->start_date)->copy()->addDays($duration - 1);
    }

    /**
     * Sum frozen days for all NON-CANCELLED freezes.
     * Optionally ensure each freeze.frozen_days is correct.
     */
    public function getTotalFrozenDays(Subscription $subscription): int
    {
        $total = 0;

        $subscription->loadMissing('freezes'); // ensure relation loaded

        foreach ($subscription->freezes as $freeze) {
            if ($freeze->status === 'cancelled') {
                continue;
            }
            $start = Carbon::parse($freeze->freeze_start_date);
            $end   = Carbon::parse($freeze->freeze_end_date);

            $correctDays = $this->computeFrozenDays($start, $end);

            // Self-heal frozen_days on stale rows
            if ((int)$freeze->frozen_days !== $correctDays) {
                $freeze->forceFill(['frozen_days' => $correctDays])->save();
            }

            $total += $correctDays;
        }

        return $total;
    }

    /**
     * Recalculate schedule from scratch (idempotent):
     * - end_date  = baseline_end + total_frozen_days
     * - next_charge_date (if auto_charge) mirrors end_date
     * - status    = paused if today inside any active/scheduled freeze, else keep current (unless you want to force active)
     */
    public function recalcSchedule(Subscription $subscription): void
    {
        // Compute baseline end
        $baselineEnd = $this->getBaselineEndDate($subscription);
        if (!$baselineEnd) {
            // Nothing to do if we don't have start or duration
            return;
        }

        // Sum all NON-CANCELLED freezes
        $totalFrozenDays = $this->getTotalFrozenDays($subscription);

        $newEnd = $baselineEnd->copy()->addDays($totalFrozenDays);
        $updates = ['end_date' => $newEnd];

        // If auto_charge: align next_charge_date to end_date
        if ($subscription->auto_charge) {
            $updates['next_charge_date'] = $newEnd->copy();
        }

        // Determine current paused/active based on whether today is inside any non-cancelled freeze
        $today = now()->startOfDay();
        $isFrozenToday = $subscription->freezes()
            ->where('status', '!=', 'cancelled')
            ->whereDate('freeze_start_date', '<=', $today)
            ->whereDate('freeze_end_date', '>=', $today)
            ->exists();

        if ($isFrozenToday && $subscription->status !== 'paused') {
            $updates['status'] = 'paused';
        } elseif (!$isFrozenToday && $subscription->status === 'paused') {
            // Optional: only flip back to active automatically if you want
            $updates['status'] = 'active';
        }

        $subscription->fill($updates)->save();
    }

    /**
     * Create/schedule a freeze and then recompute schedule.
     */
    public function scheduleFreeze(Subscription $subscription, Carbon $start, Carbon $end, ?string $reason = null, ?int $approvedBy = null): SubscriptionFreeze
    {
        if (!method_exists($subscription, 'canFreeze') || !$subscription->canFreeze()) {
            abort(422, 'This subscription cannot be frozen.');
        }
        if ($end->lt($start)) {
            abort(422, 'Freeze end date must be after or equal to start date.');
        }

        // Disallow overlap against any non-cancelled freeze
        $overlap = SubscriptionFreeze::overlapping($subscription->id, $start, $end)
            ->where('status', '!=', 'cancelled')
            ->exists();
        if ($overlap) {
            abort(422, 'Selected dates overlap an existing freeze.');
        }

        $frozenDays = $this->computeFrozenDays($start, $end);

        return DB::transaction(function () use ($subscription, $start, $end, $frozenDays, $reason, $approvedBy) {
            $freeze = $subscription->freezes()->create([
                'freeze_start_date' => $start->toDateString(),
                'freeze_end_date'   => $end->toDateString(),
                'frozen_days'       => $frozenDays,
                'reason'            => $reason,
                'status'            => ($start->isToday() || $start->isPast()) ? 'active' : 'scheduled',
                'approved_by'       => $approvedBy,
            ]);

            // Recompute schedule from source of truth
            $this->recalcSchedule($subscription);

            return $freeze;
        });
    }

    /**
     * Daily status flips for freezes; then fully recalc affected subscriptions.
     */
    public function dailyReconcile(): void
    {
        $today = now()->startOfDay();

        // Start freezes that begin today
        SubscriptionFreeze::where('status', 'scheduled')
            ->whereDate('freeze_start_date', '<=', $today)
            ->whereDate('freeze_end_date', '>=', $today)
            ->with('subscription')
            ->get()
            ->each(function (SubscriptionFreeze $freeze) {
                $freeze->update(['status' => 'active']);
                $this->recalcSchedule($freeze->subscription);
            });

        // Complete freezes that ended before today
        SubscriptionFreeze::where('status', 'active')
            ->whereDate('freeze_end_date', '<', $today)
            ->with('subscription')
            ->get()
            ->each(function (SubscriptionFreeze $freeze) {
                $freeze->update(['status' => 'completed']);
                $this->recalcSchedule($freeze->subscription);
            });
    }
}
