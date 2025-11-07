<?php

namespace App\Services;

use App\Models\User;
use App\Models\CMS\Tax;
use Stripe\StripeClient;
use App\Models\Sales\CheckoutLink;
use App\Models\Sales\Subscription;
use App\Models\Catalog\MealPackage;
use App\Models\Catalog\MealPackagePrice;

class StripeService
{
    protected $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('cashier.secret'));
    }

    /**
     * Create a Product on Stripe for a MealPackage.
     */
    public function createProduct(MealPackage $mealPackage)
    {
        if ($mealPackage->stripe_product_id) {
            return $mealPackage->stripe_product_id;
        }

        $meal = $mealPackage->meal;
        $package = $mealPackage->package;

        $payload = [
            'name' => "{$meal->name} - {$package->name}",
            'description' => $package->tagline ?? '',
            'metadata' => [
                'meal_id' => $meal->id,
                'package_id' => $package->id,
                'meal_package_id' => $mealPackage->id,
            ],
        ];

        $product = $this->stripe->products->create($payload);

        $mealPackage->update(['stripe_product_id' => $product->id]);
        $mealPackage->refresh();
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
    public function createPrice(MealPackagePrice $mealPackagePrice)
    {
        $mealPackage = $mealPackagePrice->mealPackage;

        // Ensure product exists on Stripe
        if (!$mealPackage->stripe_product_id) {
            $this->createProduct($mealPackage);
        }


        if ($mealPackagePrice->stripe_price_id) {
            return $mealPackagePrice->stripe_price_id;
        }

        $price = $this->stripe->prices->create([
            'unit_amount' => intval($mealPackagePrice->price * 100),
            'currency' => 'aed',
            'product' => $mealPackage->stripe_product_id,
            'nickname' => "{$mealPackagePrice->duration} Days – {$mealPackagePrice->calorie->label} kcal",
            'metadata' => [
                'meal_package_price_id' => $mealPackagePrice->id,
                'calorie_id'            => $mealPackagePrice->calorie_id,
                'calorie'               => $mealPackagePrice->calorie->label,
                'duration'              => $mealPackagePrice->duration,
                'discount_percent'      => $mealPackagePrice->discount_percent,
            ],
        ]);


        $mealPackagePrice->update(['stripe_price_id' => $price->id]);
        return $price->id;
    }

    public function deactivatePrice($stripePriceId)
    {
        $this->stripe->prices->update($stripePriceId, ['active' => false]);
    }

    /**
     * Create a Stripe Checkout Session for a Checkout Link
     */
    public function createCheckoutSession(CheckoutLink $checkout)
    {
        $priceId = $checkout->mealPackagePrice->stripe_price_id;
        $user   = $checkout->user;

        if (!$user->stripe_id) {
            $user->createAsStripeCustomer();
        }

        $tax = $checkout->address->country->tax;

        $stripeTaxId = $tax->stripe_id;

        if (is_null($stripeTaxId)) {
            $stripeTaxId = $this->syncTaxRate($tax);
        }

        $sessionData = [
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'mode' => 'payment',
            'customer' => $user->stripe_id,
            'payment_intent_data' => [
                'setup_future_usage' => 'off_session', // Save and auto charge for future
            ],
            'line_items' => [[
                'price' => $priceId,
                'quantity' => 1,
                'tax_rates' => [$stripeTaxId]
            ]],
            'metadata' => [
                'checkout_link_id' => $checkout->id,
                'meal_id' => $checkout->meal_id,
                'meal_package_id' => $checkout->meal_package_id,
                'meal_package_price_id' => $checkout->meal_package_price_id,
                'customer_email'  => $user->email,
                'customer_phone'  => $user->phone,
            ],
            'success_url' => route('checkout.success', ['checkout' => $checkout->id]),
            'cancel_url'  => route('checkout.cancel', ['checkout' => $checkout->id]),
        ];

        $session = $this->stripe->checkout->sessions->create($sessionData);

        $checkout->update([
            'stripe_session_id' => $session->id,
            'stripe_checkout_url' => $session->url,
        ]);

        return $session->url;
    }

    /**
     * Retrieve checkout session and return payment method ID
     */
    public function getPaymentMethodFromSession(string $sessionId): ?string
    {
        try {
            $session = $this->stripe->checkout->sessions->retrieve($sessionId, [
                'expand' => ['payment_intent.payment_method'],
            ]);

            return $session->payment_intent->payment_method ?? null;
        } catch (\Exception $e) {
            logger()->error('StripeService@getPaymentMethodFromSession error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Attach payment method to user on Stripe and set as default
     */
    public function attachPaymentMethodToUser(User $user, string $paymentMethodId): ?string
    {
        try {
            if (!$user->stripe_id) {
                $user->createAsStripeCustomer();
            }

            $this->stripe->paymentMethods->attach($paymentMethodId, [
                'customer' => $user->stripe_id,
            ]);

            $user->updateDefaultPaymentMethod($paymentMethodId);

            return $paymentMethodId;
        } catch (\Exception $e) {
            logger()->error('StripeService@attachPaymentMethodToUser error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Save user’s card from checkout session
     */
    public function saveUserPaymentMethod(User $user, ?string $stripeSessionId): ?string
    {
        if (!$stripeSessionId) {
            return null;
        }

        $paymentMethodId = $this->getPaymentMethodFromSession($stripeSessionId);

        if ($paymentMethodId) {
            return $this->attachPaymentMethodToUser($user, $paymentMethodId);
        }

        return null;
    }

    public function getPaymentIntentId(string $sessionId): ?string
    {
        try {
            $session = $this->stripe->checkout->sessions->retrieve($sessionId);
            return $session->payment_intent ?? null;
        } catch (\Exception $e) {
            logger()->error('StripeService@getPaymentIntentId error: ' . $e->getMessage());
            return null;
        }
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

    public function chargeSubscriptionRenewal(Subscription $subscription, float $amount, $tax = null)
    {
        $customer = $subscription->user;
        $taxRateId = $tax?->stripe_id;

        $params = [
            'amount' => intval($amount * 100),
            'currency' => 'aed',
            'customer' => $customer->stripe_id,
            'payment_method' => $subscription->payment_method_id,
            'off_session' => true,
            'confirm' => true,
            'description' => "Auto-Renewal: {$subscription->mealPackage->meal->name} | ({$subscription->mealPackage->package->name}) | {$subscription->mealPackagePrice->duration} Days",
            'metadata' => [
                'subscription_id' => $subscription->id,
                'stripe_product_id' => $subscription->mealPackage->stripe_product_id,
                'stripe_price_id' => $subscription->mealPackagePrice->stripe_price_id,
            ],
        ];

        if ($taxRateId) {
            $params['tax'] = ['tax_rates' => [$taxRateId]];
        }

        $intent = $this->stripe->paymentIntents->create($params);

        // Expand to get charge details (to access receipt URL)
        $intent = $this->stripe->paymentIntents->retrieve($intent->id, [
            'expand' => ['charges.data.balance_transaction'],
        ]);

        $receiptUrl = null;
        if (!empty($intent->charges->data[0]->receipt_url)) {
            $receiptUrl = $intent->charges->data[0]->receipt_url;
        }

        // Return both intent and receipt link
        return (object)[
            'intent' => $intent,
            'receipt_url' => $receiptUrl,
        ];
    }
}
