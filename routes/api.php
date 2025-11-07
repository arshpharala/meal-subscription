<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\StripeWebhookController;

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])
    ->name('webhook.subscription.success');
