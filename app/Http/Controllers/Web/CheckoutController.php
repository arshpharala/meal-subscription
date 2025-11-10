<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Services\StripeService;
use App\Models\Sales\PaymentLink;
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

    public function show(string $paymentLinkId)
    {
        $paymentLink = PaymentLink::with([
            'user',
            'meal',
            'mealPackage',
            'mealPackagePrice.calorie',
        ])->where('status',  'pending')->findOrFail($paymentLinkId);

        if ($paymentLink->stripe_checkout_url) {
            return redirect()->to($paymentLink->stripe_checkout_url);
        }

        $url = $this->stripeService->createCheckoutSession($paymentLink);
        return redirect()->to($url);
    }

    public function success(string $paymentLinkId)
    {

        $paymentLink = PaymentLink::with([
            'user',
            'meal',
            'mealPackage',
            'mealPackagePrice.calorie',
        ])->findOrFail($paymentLinkId);

        if ($paymentLink->status !== 'paid') {
            DB::transaction(function () use ($paymentLink) {
                $paymentLink->update(['status' => 'paid']);
                $user = $paymentLink->user;

                $paymentMethodId = $this->stripeService->saveUserPaymentMethod($user, $paymentLink->stripe_session_id);
                $paymentIntentId = $this->stripeService->getPaymentIntentId($paymentLink->stripe_session_id);

                if (now()->isSameDay($paymentLink->start_date)) {
                    $status = 'active';
                } else {
                    $status = 'scheduled';
                }


                $user->subscriptions()->create([
                    'type' => 'meal',
                    'reference' => $paymentIntentId, // Stripe payment reference
                    'stripe_id' => $paymentLink->stripe_session_id ?? 'manual_' . uniqid(),
                    'stripe_status' => 'paid',
                    'stripe_price' => $paymentLink->mealPackagePrice->stripe_price_id,
                    'payment_method_id' => $paymentMethodId,
                    'quantity' => 1,
                    'meal_package_id' => $paymentLink->meal_package_id,
                    'meal_package_price_id' => $paymentLink->meal_package_price_id,
                    'start_date' => $paymentLink->start_date,
                    'end_date' => $paymentLink->end_date,
                    'next_charge_date' => $paymentLink->end_date,
                    'auto_charge' => $paymentLink->is_recurring ?? false,
                    'status' => $status,
                    'sub_total' => $paymentLink->sub_total,
                    'tax_amount' => $paymentLink->tax_amount,
                    'total' => $paymentLink->total,
                    'currency_id' => $paymentLink->currency_id ?? 1, // AED
                    'description' => $paymentLink->description,
                ]);
            });

            $paymentLink->user->notify(new PaymentSuccessNotification($paymentLink));
        }

        return view('theme.meals.checkouts.success', compact('paymentLink'));
    }

    public function cancel(string $paymentLinkId)
    {
        $paymentLink = PaymentLink::findOrFail($paymentLinkId);

        if ($paymentLink->status !== 'cancelled') {
            $paymentLink->update(['status' => 'cancelled']);

            $paymentLink->user->notify(new PaymentCancelledNotification($paymentLink));
        }

        return view('theme.meals.checkouts.cancel', compact('paymentLink'));
    }
}
