<?php

namespace App\Models\Catalog;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

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

    /**
     * Generates a descriptive string for the meal package price.
     *
     * This method constructs a description that includes the meal name,
     * package label and tagline, duration in days, and optionally the
     * calorie label, formatted as a pipe-separated string.
     *
     * @param bool $kcalLabel Whether to include the calorie label in the description. Defaults to true.
     * @return string The formatted description string.
     */
    function getDescription($kcalLabel = true): string
    {
        $mealPackage = $this->mealPackage;
        $meal = $mealPackage->meal;
        $package = $mealPackage->package;
        $durationLabel = $this->duration . ' ' .  Str::plural('day', $this->duration);
        $description = "{$meal->name} | {$package->label} ({$package->tagline}) | {$durationLabel}";

        if ($kcalLabel) {
            $kcal = $this->calorie->label;
            $description .= " | {$kcal}";
        }

        return $description;
    }

    function getDurationLabel(): string
    {
        $durationLabel = $this->duration . ' ' .  Str::plural('day', $this->duration);
        return $durationLabel;
    }

    /**
     * Calculates the base amount, tax amount, and total amount based on the given tax percentage.
     *
     * This method takes the price from the model, computes the tax amount using the provided
     * tax percentage, and returns an array containing the original amount, the calculated tax,
     * and the total amount including tax.
     *
     * @param float $taxPercentage The tax percentage to apply (e.g., 10 for 10%).
     * @return array An associative array with keys 'sub_total' (base price), 'tax_amount' (calculated tax), and 'total_amount' (sum of amount and tax).
     */
    function calculateAmounts($taxPercentage): array
    {
        $subTotal = $this->price;
        $taxAmount = round($subTotal * ($taxPercentage / 100), 2);
        $total = $subTotal + $taxAmount;

        return [
            'sub_total' => $subTotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $total,
        ];
    }
}
