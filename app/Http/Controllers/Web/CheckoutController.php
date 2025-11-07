<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Services\StripeService;
use App\Models\Sales\CheckoutLink;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PaymentSuccessNotification;
use App\Notifications\PaymentCancelledNotification;

class CheckoutController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public function show(string $checkoutId)
    {
        $checkout = CheckoutLink::with([
            'user',
            'meal',
            'mealPackage',
            'mealPackagePrice.calorie',
        ])->where('status',  'pending')->findOrFail($checkoutId);

        if ($checkout->stripe_checkout_url) {
            return redirect()->to($checkout->stripe_checkout_url);
        }

        $url = $this->stripeService->createCheckoutSession($checkout);
        return redirect()->to($url);
    }

    public function success(string $checkoutId)
    {
        $checkout = CheckoutLink::with([
            'user',
            'meal',
            'mealPackage',
            'mealPackagePrice.calorie',
        ])->findOrFail($checkoutId);

        if ($checkout->status !== 'paid') {
            DB::transaction(function () use ($checkout) {
                $checkout->update(['status' => 'paid']);
                $user = $checkout->user;

                $paymentMethodId = $this->stripeService->saveUserPaymentMethod($user, $checkout->stripe_session_id);
                $paymentIntentId = $this->stripeService->getPaymentIntentId($checkout->stripe_session_id);

                $durationDays = $checkout->mealPackagePrice->duration;
                $startDate = now();
                $endDate = now()->addDays($durationDays)->subDay();

                $user->subscriptions()->create([
                    'type' => 'meal',
                    'reference' => $paymentIntentId, // Stripe payment reference
                    'stripe_id' => $checkout->stripe_session_id ?? 'manual_' . uniqid(),
                    'stripe_status' => 'paid',
                    'stripe_price' => $checkout->mealPackagePrice->stripe_price_id,
                    'payment_method_id' => $paymentMethodId,
                    'quantity' => 1,
                    'meal_package_id' => $checkout->meal_package_id,
                    'meal_package_price_id' => $checkout->meal_package_price_id,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'next_charge_date' => $endDate,
                    'auto_charge' => $checkout->is_recurring ?? false,
                    'status' => 'active',
                    'address_id' => $checkout->address_id,
                ]);
            });

            $checkout->user->notify(new PaymentSuccessNotification($checkout));
        }

        return view('checkout.success', compact('checkout'));
    }

    public function cancel(string $checkoutId)
    {
        $checkout = CheckoutLink::findOrFail($checkoutId);

        if ($checkout->status !== 'cancelled') {
            $checkout->update(['status' => 'cancelled']);

            $checkout->user->notify(new PaymentCancelledNotification($checkout));
        }

        return view('checkout.cancel', compact('checkout'));
    }
}
