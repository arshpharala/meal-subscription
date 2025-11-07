<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class SubscriptionFreeze extends Model
{
    use HasUuids;

    protected $fillable = [
        'subscription_id',
        'freeze_start_date',
        'freeze_end_date',
        'frozen_days',
        'reason',
        'status',
        'approved_by',
    ];

    protected $casts = [
        'freeze_start_date' => 'date',
        'freeze_end_date'   => 'date',
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function scopeOverlapping($query, $subscriptionId, $start, $end)
    {
        return $query->where('subscription_id', $subscriptionId)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('freeze_start_date', [$start, $end])
                    ->orWhereBetween('freeze_end_date', [$start, $end])
                    ->orWhere(function ($qq) use ($start, $end) {
                        $qq->where('freeze_start_date', '<=', $start)
                            ->where('freeze_end_date', '>=', $end);
                    });
            });
    }
}
