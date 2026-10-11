<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Events\WebhookReceived;
use Mockery;
use Tests\TestCase;

class StripeWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_failed_payment_webhook_is_logged_as_a_warning(): void
    {
        Log::spy();

        event(new WebhookReceived([
            'type' => 'invoice.payment_failed',
            'data' => ['object' => ['id' => 'in_123', 'customer' => 'cus_123']],
        ]));

        Log::shouldHaveReceived('warning')
            ->once()
            ->with('Stripe payment failed', Mockery::on(
                fn (array $context) => $context['type'] === 'invoice.payment_failed'
                    && $context['customer'] === 'cus_123',
            ));
    }

    public function test_subscription_webhook_is_logged_as_information(): void
    {
        Log::spy();

        event(new WebhookReceived([
            'type' => 'customer.subscription.updated',
            'data' => ['object' => ['id' => 'sub_123', 'customer' => 'cus_123']],
        ]));

        Log::shouldHaveReceived('info')
            ->once()
            ->with('Stripe webhook received', Mockery::on(
                fn (array $context) => $context['type'] === 'customer.subscription.updated',
            ));
    }
}
