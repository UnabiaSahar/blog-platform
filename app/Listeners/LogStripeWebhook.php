<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Events\WebhookReceived;

class LogStripeWebhook
{
    /**
     * Record Stripe webhook events and flag failed payments.
     *
     * Cashier's own webhook controller keeps subscriptions, their status, and
     * grace periods in sync with Stripe; this listener is the application-side
     * hook for observability and any future app-specific reactions.
     */
    public function handle(WebhookReceived $event): void
    {
        $type = $event->payload['type'] ?? 'unknown';

        $context = [
            'type' => $type,
            'stripe_id' => $event->payload['data']['object']['id'] ?? null,
            'customer' => $event->payload['data']['object']['customer'] ?? null,
        ];

        if ($type === 'invoice.payment_failed') {
            Log::warning('Stripe payment failed', $context);

            return;
        }

        Log::info('Stripe webhook received', $context);
    }
}
