<?php

namespace App\Models\Sales;

use App\Models\Address;
use App\Models\Catalog\MealPackage;
use App\Models\Catalog\MealPackagePrice;
use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Subscription as CashierSubscription;

class Subscription extends CashierSubscription
{

    public $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'next_charge_date' => 'date',
    ];

    public function freezes()
    {
        return $this->hasMany(SubscriptionFreeze::class);
    }

    // Is currently paused?
    public function getIsPausedAttribute(): bool
    {
        return $this->status === 'paused';
    }

    // Guard: can freeze?
    public function canFreeze(): bool
    {
        return in_array($this->status, ['active', 'payment_failed']);
    }

    public function mealPackagePrice()
    {
        return $this->belongsTo(MealPackagePrice::class);
    }
    public function mealPackage()
    {
        return $this->belongsTo(MealPackage::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function renewalLogs()
    {
        return $this->hasMany(\App\Models\Sales\SubscriptionRenewalLog::class);
    }


    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'active' => '<span class="badge bg-success">Active</span>',
            'cancelled' => '<span class="badge bg-danger">Cancelled</span>',
            'paused' => '<span class="badge bg-info">Paused</span>',
            'payment_failed' => '<span class="badge bg-dark">Payment Failed</span>',
            default => '<span class="badge bg-secondary">' . e(ucfirst($this->status ?? 'Unknown')) . '</span>',
        };
    }
}
