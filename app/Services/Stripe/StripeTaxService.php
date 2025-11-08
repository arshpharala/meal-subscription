<?php

namespace App\Services\Stripe;

use App\Models\CMS\Tax;
use App\Models\Catalog\MealPackage;
use App\Models\Catalog\MealPackagePrice;

class StripeTaxService extends StripeClientService
{

    public function __construct()
    {
        parent::__construct();
    }

    public function syncTaxRate(Tax $tax): ?string
    {
        try {
            if ($tax->stripe_id) {
                try {
                    $rate = $this->stripe->taxRates->retrieve($tax->stripe_id);

                    if ((bool)$rate->active !== (bool)$tax->is_active) {
                        $this->stripe->taxRates->update($rate->id, [
                            'active' => (bool)$tax->is_active,
                        ]);
                    }

                    return $tax->stripe_id;
                } catch (\Stripe\Exception\InvalidRequestException) {
                    logger()->info("Stripe tax rate {$tax->stripe_id} not found, re-syncing...");
                }
            }

            if ($existing = $this->findExistingTaxRate($tax)) {
                $tax->update(['stripe_id' => $existing->id]);
                return $existing->id;
            }

            $created = $this->createTaxRate($tax);
            if ($created) {
                $tax->update(['stripe_id' => $created->id]);
                return $created->id;
            }

            return null;
        } catch (\Throwable $e) {
            logger()->error('StripeService@syncTaxRate: ' . $e->getMessage());
            return null;
        }
    }

    protected function findExistingTaxRate(Tax $tax): ?object
    {
        try {
            $rates = $this->stripe->taxRates->all(['limit' => 100])->data ?? [];

            foreach ($rates as $rate) {
                if (
                    strcasecmp($rate->display_name, $tax->label) === 0 &&
                    abs($rate->percentage - (float)$tax->percentage) < 0.01
                ) {
                    return $rate;
                }
            }
        } catch (\Throwable $e) {
            logger()->warning('StripeService@findExistingTaxRate: ' . $e->getMessage());
        }

        return null;
    }

    protected function createTaxRate(Tax $tax): ?object
    {
        try {
            return $this->stripe->taxRates->create([
                'display_name' => $tax->label,
                'description'  => "{$tax->label} ({$tax->percentage}%)",
                'percentage'   => (float)$tax->percentage,
                'inclusive'    => false,
                'active'       => (bool)$tax->is_active,
                'jurisdiction' => 'AE', // make dynamic if needed
            ]);
        } catch (\Throwable $e) {
            logger()->error('StripeService@createTaxRate: ' . $e->getMessage());
            return null;
        }
    }
}
