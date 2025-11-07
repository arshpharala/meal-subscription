<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MealPackagePrice extends Model
{
    use HasUuids;

    protected $fillable = ['meal_package_id', 'calorie_id', 'duration', 'extra_charges_id', 'price', 'discount_percent', 'is_active', 'stripe_price_id'];

    public $casts = [
        'is_active' => 'boolean'
    ];

    function scopeActive($query)
    {
        $query->where('is_active', true);
    }

    function calorie()
    {
        return $this->belongsTo(Calorie::class);
    }

    function mealPackage()
    {
        return $this->belongsTo(MealPackage::class);
    }

    public static function isDuplicate(string $mealPackageId, string $calorieId, int $duration, $exceptId = null): bool
    {
        return self::where('meal_package_id', $mealPackageId)
            ->where('calorie_id', $calorieId)
            ->where('duration', $duration)
            ->when($exceptId, function ($q) use ($exceptId) {
                $q->where('id', '!=', $exceptId);
            })
            ->exists();
    }
}
