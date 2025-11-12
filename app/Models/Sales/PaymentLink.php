<?php

namespace App\Models\Sales;

use App\Models\Address;
use App\Models\User;
use App\Models\Catalog\Meal;
use App\Models\Catalog\MealPackage;
use Illuminate\Database\Eloquent\Model;
use App\Models\Catalog\MealPackagePrice;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PaymentLink extends Model
{
    use HasUuids;

    protected $fillable = [
        'id',
        'user_id',
        'meal_id',
        'meal_package_id',
        'meal_package_price_id',
        'start_date',
        'end_date',
        'is_recurring',
        'stripe_session_id',
        'stripe_checkout_url',
        'status',
        'email_sent',
        'portal_url',
        'address_id',
        'sub_total',
        'tax_amount',
        'total',
        'currency_id',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function meal()
    {
        return $this->belongsTo(Meal::class);
    }

    public function mealPackage()
    {
        return $this->belongsTo(MealPackage::class);
    }

    public function mealPackagePrice()
    {
        return $this->belongsTo(MealPackagePrice::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function scopeWithJoins($query)
    {
        $query->leftJoin('users', 'users.id', 'payment_links.user_id')
            ->leftJoin('meal_package_prices', 'meal_package_prices.id', 'payment_links.meal_package_price_id')
            ->leftJoin('calories', 'calories.id', 'meal_package_prices.calorie_id')
            ->leftJoin('meal_packages', 'meal_packages.id', 'payment_links.meal_package_id')
            ->leftJoin('packages', 'packages.id', 'meal_packages.package_id')
            ->leftJoin('meals', 'meals.id', 'payment_links.meal_id')
        ;
    }

    public function scopeWithSelection($query)
    {
        $query->select(
            'payment_links.id',
            'payment_links.start_date',
            'payment_links.is_recurring',
            'payment_links.email_sent',
            'payment_links.status',
            'payment_links.created_at',
            'users.id as user_id',
            'users.name as user_name',
            'users.email as user_email',
            'meals.name as meal_name',
            'packages.name as package_name',
            'packages.tagline as package_tagline',
            'meal_package_prices.duration',
            'calories.label as calorie_label',
        );
    }
}
