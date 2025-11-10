<?php

namespace App\Services;

use App\Models\User;
use App\Models\CMS\Tax;
use App\Models\Sales\PaymentLink;
use App\Models\Sales\Subscription;
use App\Models\Catalog\MealPackage;
use App\Models\Catalog\MealPackagePrice;
use App\Services\Stripe\StripeCatalogService;
use App\Services\Stripe\StripeClientService;
use App\Services\Stripe\StripeTaxService;
use Illuminate\Support\Facades\Log;

class StripeService extends StripeClientService
{
    public function __construct()
    {
        parent::__construct();
    }

    /* ============================================================
     | PAYMNT LINK SESSION
     ============================================================ */
    public function createCheckoutSession(PaymentLink $paymentLink): string
    {
        $user = $paymentLink->user;
        if (!$user->stripe_id) {
            $user->createAsStripeCustomer();
        }

        $tax = $paymentLink->address->country->tax ?? null;
        $stripeTaxId = $tax?->stripe_id ?: (new StripeTaxService())->syncTaxRate($tax);

        $session = $this->stripe->checkout->sessions->create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'customer' => $user->stripe_id,
            'payment_intent_data' => ['setup_future_usage' => 'off_session'],
            'line_items' => [[
                'price' => $paymentLink->mealPackagePrice->stripe_price_id,
                'quantity' => 1,
                'tax_rates' => [$stripeTaxId],
            ]],
            'metadata' => [
                'checkout_link_id' => $paymentLink->id,
                'meal_package_price_id' => $paymentLink->meal_package_price_id,
            ],
            'success_url' => route('payment.success', ['id' => $paymentLink->id]),
            'cancel_url'  => route('payment.cancel',  ['id' => $paymentLink->id]),
        ]);

        $paymentLink->update([
            'stripe_session_id' => $session->id,
            'stripe_checkout_url' => $session->url,
        ]);

        return $session->url;
    }

    /* ============================================================
     | PAYMENT METHOD MANAGEMENT
     ============================================================ */
    public function getPaymentMethodFromSession(string $sessionId): ?string
    {
        try {
            $session = $this->stripe->checkout->sessions->retrieve($sessionId, [
                'expand' => ['payment_intent.payment_method'],
            ]);
            return $session->payment_intent->payment_method ?? null;
        } catch (\Throwable $e) {
            $this->logError('getPaymentMethodFromSession', $e);
            return null;
        }
    }

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
        } catch (\Throwable $e) {
            $this->logError('attachPaymentMethodToUser', $e);
            return null;
        }
    }

    public function saveUserPaymentMethod(User $user, ?string $stripeSessionId): ?string
    {
        if (!$stripeSessionId) return null;

        $method = $this->getPaymentMethodFromSession($stripeSessionId);
        return $method ? $this->attachPaymentMethodToUser($user, $method) : null;
    }

    public function getPaymentIntentId(string $sessionId): ?string
    {
        try {
            $session = $this->stripe->checkout->sessions->retrieve($sessionId);
            return $session->payment_intent ?? null;
        } catch (\Throwable $e) {
            $this->logError('getPaymentIntentId', $e);
            return null;
        }
    }

    /* ============================================================
     | INVOICE-BASED RENEWAL (Stripe Tax Supported)
     ============================================================ */
    public function chargeSubscriptionRenewal(Subscription $subscription): object
    {
        try {
            $user = $subscription->user;
            if (!$user->stripe_id) {
                $user->createAsStripeCustomer();
            }

            $priceId = $subscription->mealPackagePrice->stripe_price_id;
            $productId = $subscription->mealPackage->stripe_product_id;
            $tax = $subscription->address->country->tax ?? null;
            $taxRateId = $tax?->stripe_id ?: (new StripeTaxService())->syncTaxRate($tax);

            // Prevent multiple draft invoices
            $existing = $this->stripe->invoices->all([
                'customer' => $user->stripe_id,
                'status'   => 'draft',
                'limit'    => 1,
            ])->data ?? [];

            if (!empty($existing)) {
                $this->stripe->invoices->voidInvoice($existing[0]->id);
            }

            // Create Invoice Item
            $invoiceItem = [
                'customer'    => $user->stripe_id,
                'price'       => $priceId,
                'quantity'    => 1,
                'description' => "Auto-Renewal: {$subscription->mealPackagePrice->getDescription()})",
            ];

            if ($taxRateId) $invoiceItem['tax_rates'] = [$taxRateId];
            $this->stripe->invoiceItems->create($invoiceItem);

            // Create Invoice
            $invoice = $this->stripe->invoices->create([
                'customer' => $user->stripe_id,
                'auto_advance' => true,
                'collection_method' => 'charge_automatically',
                'metadata' => [
                    'subscription_id' => $subscription->id,
                    'stripe_product_id' => $productId,
                    'stripe_price_id' => $priceId,
                ],
            ]);

            // Finalize + Pay
            $this->stripe->invoices->finalizeInvoice($invoice->id);
            $paid = $this->stripe->invoices->pay($invoice->id);

            // Standard Response Object
            return (object) [
                'status'      => 'success',
                'reference'   => $paid->id,
                'receipt_url' => $paid->hosted_invoice_url,
                'subtotal'    => $paid->subtotal / 100,
                'tax_amount'  => collect($paid->total_tax_amounts)->sum(fn($t) => $t->amount) / 100,
                'total'       => $paid->total / 100,
            ];
        } catch (\Throwable $e) {
            $this->logError('chargeSubscriptionRenewal', $e);
            return (object) [
                'status' => 'failed',
                'message' => $e->getMessage(),
            ];
        }
    }

    /* ============================================================
     | UTILITIES
     ============================================================ */
    protected function logError(string $context, \Throwable $e): void
    {
        Log::error("[StripeService@$context] " . $e->getMessage());
    }
}
