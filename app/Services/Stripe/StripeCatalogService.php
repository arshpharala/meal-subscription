<?php

namespace App\Services\Stripe;

use App\Models\Catalog\MealPackage;
use App\Models\Catalog\MealPackagePrice;

class StripeCatalogService extends StripeClientService
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Create a Product on Stripe for a MealPackage.
     */
    public function createProduct(MealPackage $mealPackage): string
    {
        if ($mealPackage->stripe_product_id) {
            return $mealPackage->stripe_product_id;
        }

        $product = $this->stripe->products->create([
            'name'        => "{$mealPackage->meal->name} - {$mealPackage->package->name}",
            'description' => $mealPackage->package->tagline ?? '',
            'metadata'    => [
                'meal_id'        => $mealPackage->meal_id,
                'package_id'     => $mealPackage->package_id,
                'meal_package_id' => $mealPackage->id,
            ],
        ]);

        $mealPackage->update(['stripe_product_id' => $product->id]);
        return $product->id;
    }

    /**
     * Delete product from Stripe (optional cleanup).
     */
    public function deleteProduct(MealPackage $mealPackage)
    {
        if ($mealPackage->stripe_product_id) {
            $this->stripe->products->update(
                $mealPackage->stripe_product_id,
                ['active' => false]
            );
        }
    }

    /**
     * Create Stripe Price for a MealPackagePrice
     */
    public function createPrice(MealPackagePrice $mealPackagePrice): string
    {
        $mealPackage = $mealPackagePrice->mealPackage;

        if (!$mealPackage->stripe_product_id) {
            $this->createProduct($mealPackage);
        }

        if ($mealPackagePrice->stripe_price_id) {
            return $mealPackagePrice->stripe_price_id;
        }

        $price = $this->stripe->prices->create([
            'unit_amount' => intval($mealPackagePrice->price * 100),
            'currency'    => 'aed',
            'product'     => $mealPackage->stripe_product_id,
            'nickname'    => "{$mealPackagePrice->duration} Days – {$mealPackagePrice->calorie->label} kcal",
            'metadata'    => [
                'meal_package_price_id' => $mealPackagePrice->id,
                'duration'              => $mealPackagePrice->duration,
                'calorie'               => $mealPackagePrice->calorie->label,
            ],
        ]);

        $mealPackagePrice->update(['stripe_price_id' => $price->id]);
        return $price->id;
    }

    /**
     * Deactivate Stripe Price for a MealPackagePrice
     */
    public function deactivatePrice($stripePriceId): void
    {
        $this->stripe->prices->update($stripePriceId, ['active' => false]);
    }
}
