<?php

namespace App\Services;

use App\Models\Address;
use App\Models\User;
use App\Models\Catalog\MealPackagePrice;
use App\Models\Sales\PaymentLink;
use Illuminate\Support\Facades\URL;

class PaymentLinkService
{
    /**
     * Create a new Payment link record and generate a temporary portal URL.
     */
    public function create(User $customer, array $data): array
    {
        $mealPackagePrice   = MealPackagePrice::findOrFail($data['meal_package_price_id']);
        $address            = Address::findOrFail($data['address_id']);
        $country            = $address->country;
        $tax                = $country->tax;
        $meal               = $mealPackagePrice->mealPackage->meal;
        $amounts            = $mealPackagePrice->calculateAmounts($tax->percentage);
        $description        = $mealPackagePrice->getDescription();

        $paymentLink = PaymentLink::create([
            'user_id' => $customer->id,
            'meal_id' => $meal->id,
            'meal_package_id' => $mealPackagePrice->meal_package_id,
            'meal_package_price_id' => $mealPackagePrice->id,
            'start_date' => $data['start_date'] ?? null,
            'is_recurring' => $data['is_recurring'] ?? false,
            'address_id' => $address->id,
            'status' => 'pending',
            'sub_total' => $amounts['sub_total'],
            'tax_amount' => $amounts['tax_amount'],
            'total' => $amounts['total_amount'],
            'description' => $description,
        ]);

        // Generate temporary portal URL (signed link valid for e.g. 7 days)
        $portalUrl = URL::temporarySignedRoute(
            'checkout.portal.show',
            now()->addDays(7),
            ['checkout' => $paymentLink->id]
        );

        $paymentLink->update(['portal_url' => $portalUrl]);

        return [
            'success' => true,
            'portal_url' => $portalUrl,
            'checkout_id' => $paymentLink->id,
        ];
    }
}
