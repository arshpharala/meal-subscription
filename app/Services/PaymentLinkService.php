<?php

namespace App\Services;

use App\Models\User;
use App\Models\Catalog\MealPackagePrice;
use App\Models\Sales\CheckoutLink;
use Illuminate\Support\Facades\URL;

class PaymentLinkService
{
    /**
     * Create a new checkout link record and generate a temporary portal URL.
     */
    public function create(User $customer, array $data): array
    {
        $mealPackagePrice = MealPackagePrice::findOrFail($data['meal_package_price_id']);

        $meal = $mealPackagePrice->mealPackage->meal;

        $checkout = CheckoutLink::create([
            'user_id' => $customer->id,
            'meal_id' => $meal->id,
            'meal_package_id' => $mealPackagePrice->meal_package_id,
            'meal_package_price_id' => $mealPackagePrice->id,
            'start_date' => $data['start_date'] ?? null,
            'is_recurring' => $data['is_recurring'] ?? false,
            'address_id' => $data['address_id'] ?? $customer->default_address_id,
            'status' => 'pending',
        ]);

        // Generate temporary portal URL (signed link valid for e.g. 7 days)
        $portalUrl = URL::temporarySignedRoute(
            'checkout.portal.show',
            now()->addDays(7),
            ['checkout' => $checkout->id]
        );

        $checkout->update(['portal_url' => $portalUrl]);

        return [
            'success' => true,
            'portal_url' => $portalUrl,
            'checkout_id' => $checkout->id,
        ];
    }
}
