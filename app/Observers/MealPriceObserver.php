<?php

namespace App\Observers;

use App\Models\Catalog\MealPackageDuration;

class MealPriceObserver
{
    /**
     * Handle the MealPackageDuration "created" event.
     */
    public function created(MealPackageDuration $mealPackageDuration): void
    {
        $mealPackageDuration->package->meal->refreshStartingPrice();
    }

    /**
     * Handle the MealPackageDuration "updated" event.
     */
    public function updated(MealPackageDuration $mealPackageDuration): void
    {
        //
    }

    /**
     * Handle the MealPackageDuration "deleted" event.
     */
    public function deleted(MealPackageDuration $mealPackageDuration): void
    {
        $mealPackageDuration->package->meal->refreshStartingPrice();
    }

    /**
     * Handle the MealPackageDuration "restored" event.
     */
    public function restored(MealPackageDuration $mealPackageDuration): void
    {
        //
    }

    /**
     * Handle the MealPackageDuration "force deleted" event.
     */
    public function forceDeleted(MealPackageDuration $mealPackageDuration): void
    {
        //
    }
}
