<?php

namespace App\Models\Sales;

use App\Models\CMS\Currency;
use App\Models\CMS\Tax;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SubscriptionRenewalLog extends Model
{
    use HasUuids;

    protected $fillable = [
        'subscription_id',
        'reference',
        'gateway_id',
        'tax_id',
        'currency_id',
        'amount',
        'tax_amount',
        'total_amount',
        'status',
        'message',
        'charged_at',
    ];

    protected $casts = [
        'charged_at' => 'datetime',
        'amount' => 'float',
        'tax_amount' => 'float',
        'total_amount' => 'float',
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    // 💳 Payment gateway (Stripe, PayPal, Cash)
    // public function gateway()
    // {
    //     return $this->belongsTo(\App\Models\System\Gateway::class, 'gateway_id');
    // }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }


    public function isSuccessful(): bool
    {
        return $this->status === 'success';
    }
}
