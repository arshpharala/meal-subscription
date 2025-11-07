<?php

namespace App\Http\Controllers\API;

use Stripe\Webhook;
use Illuminate\Http\Request;
use App\Models\Sales\CheckoutLink;
use App\Models\Sales\Subscription;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('cashier.webhook.secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (SignatureVerificationException $e) {
            Log::error('⚠️ Stripe webhook signature verification failed: ' . $e->getMessage());
            return response('Invalid signature', 400);
        } catch (\UnexpectedValueException $e) {
            return response('Invalid payload', 400);
        }

        Log::info('stripe', ['payload' => $event]);

        return true;

        $type = $event->type;
        $object = $event->data->object;

        Log::info('🔔 Stripe webhook received: ' . $type);

        switch ($type) {
            // ✅ PAYMENT SUCCESS
            case 'checkout.session.completed':
                $this->handleCheckoutSuccess($object);
                break;

            // ⚠️ PAYMENT FAILED / CANCELED
            case 'checkout.session.expired':
            case 'payment_intent.payment_failed':
                $this->handleCheckoutFailed($object);
                break;

            default:
                Log::info("Unhandled Stripe event: {$type}");
        }

        return response('Webhook handled', 200);
    }

    /**
     * Handle successful payment
     */
    private function handleCheckoutSuccess($session)
    {
        $sessionId = $session->id;
        $customerId = $session->customer;
        $metadata = $session->metadata ?? [];

        $link = CheckoutLink::where('stripe_session_id', $sessionId)->first();

        if (!$link) {
            Log::warning("⚠️ No checkout link found for session {$sessionId}");
            return;
        }

        // Mark link as paid
        $link->update(['status' => 'paid']);

        // ✅ Create subscription entry (only after success)
        Subscription::create([
            'user_id' => $link->user_id,
            'meal_id' => $link->meal_id,
            'meal_package_id' => $link->meal_package_id,
            'meal_package_price_id' => $link->meal_package_price_id,
            'start_date' => $link->start_date,
            'status' => 'active',
            'stripe_session_id' => $link->stripe_session_id,
            'stripe_checkout_url' => $link->stripe_checkout_url,
        ]);

        Log::info("✅ Subscription activated for user {$link->user_id}");
    }

    /**
     * Handle failed or expired session
     */
    private function handleCheckoutFailed($session)
    {
        $sessionId = $session->id;
        $link = CheckoutLink::where('stripe_session_id', $sessionId)->first();

        if ($link) {
            $link->update(['status' => 'cancelled']);
            Log::info("❌ Checkout link cancelled for session {$sessionId}");
        }
    }
}
