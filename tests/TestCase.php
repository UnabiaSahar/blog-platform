<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Str;
use Laravel\Cashier\Subscription;
use Stripe\ApiRequestor;
use Stripe\HttpClient\ClientInterface;
use Stripe\HttpClient\CurlClient;

abstract class TestCase extends BaseTestCase
{
    /**
     * Create an active subscription for the given user.
     *
     * @param  array<string, mixed>  $attributes
     */
    protected function subscriptionFor(User $user, array $attributes = []): Subscription
    {
        return $user->subscriptions()->create(array_merge([
            'type' => 'default',
            'stripe_id' => 'sub_'.Str::random(24),
            'stripe_status' => 'active',
            'stripe_price' => 'price_monthly_test',
            'quantity' => 1,
        ], $attributes));
    }

    /**
     * Replace Stripe's HTTP client with one that returns canned responses,
     * keyed by the first URL fragment that matches the outgoing request.
     *
     * @param  array<string, array<string, mixed>>  $responses
     */
    protected function fakeStripe(array $responses): void
    {
        ApiRequestor::setHttpClient(new class($responses) implements ClientInterface
        {
            /**
             * @param  array<string, array<string, mixed>>  $responses
             */
            public function __construct(private array $responses) {}

            public function request($method, $absUrl, $headers, $params, $hasFile, $apiMode = 'v1', $maxNetworkRetries = null)
            {
                foreach ($this->responses as $fragment => $body) {
                    if (str_contains($absUrl, $fragment)) {
                        return [json_encode($body), 200, []];
                    }
                }

                return [json_encode([
                    'error' => ['message' => 'No fake Stripe response for '.$absUrl],
                ]), 404, []];
            }
        });
    }

    protected function tearDown(): void
    {
        ApiRequestor::setHttpClient(CurlClient::instance());

        parent::tearDown();
    }
}
