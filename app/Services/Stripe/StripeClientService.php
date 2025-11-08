<?php

namespace App\Services\Stripe;

use Stripe\StripeClient;

class StripeClientService
{
    protected StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('cashier.secret'));
    }
}
