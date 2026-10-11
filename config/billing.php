<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Billing Currency
    |--------------------------------------------------------------------------
    |
    | The currency used for display and Stripe Checkout. Individual plans must
    | use this currency when their recurring price is created in Stripe.
    |
    */

    'currency' => env('BILLING_CURRENCY', 'usd'),

    /*
    |--------------------------------------------------------------------------
    | Free Trial
    |--------------------------------------------------------------------------
    |
    | New subscribers receive a free trial before their first charge (PRD 13.4).
    | The trial is applied when the subscription is created via Stripe Checkout.
    |
    */

    'trial_days' => (int) env('BILLING_TRIAL_DAYS', 30),

    /*
    |--------------------------------------------------------------------------
    | Subscription Plans
    |--------------------------------------------------------------------------
    |
    | Prices are placeholders pending final pricing (PRD 9). `price` is the
    | display amount in the currency's minor units (e.g. cents). `stripe_price`
    | must reference a recurring Price created in the Stripe dashboard.
    |
    */

    'plans' => [
        'monthly' => [
            'name' => 'Monthly',
            'description' => 'Unlimited ad-free reading, billed every month.',
            'price' => (int) env('BILLING_PRICE_MONTHLY', 500),
            'interval' => 'month',
            'stripe_price' => env('STRIPE_PRICE_MONTHLY'),
        ],
        'annual' => [
            'name' => 'Annual',
            'description' => 'Unlimited ad-free reading, billed once a year. Two months free.',
            'price' => (int) env('BILLING_PRICE_ANNUAL', 5000),
            'interval' => 'year',
            'stripe_price' => env('STRIPE_PRICE_ANNUAL'),
        ],
    ],

];
