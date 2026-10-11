<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/billing')->assertRedirect('/login');
    }

    public function test_unverified_users_cannot_view_billing(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get('/billing')
            ->assertRedirect(route('verification.notice'));
    }

    public function test_verified_user_can_view_the_plans(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/billing')
            ->assertOk()
            ->assertSee('Monthly')
            ->assertSee('Annual')
            ->assertSee('Subscribe');
    }

    public function test_checkout_requires_a_valid_plan(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from('/billing')
            ->post('/billing/checkout', ['plan' => 'lifetime'])
            ->assertSessionHasErrors('plan');
    }

    public function test_checkout_redirects_the_user_to_stripe(): void
    {
        $this->fakeStripe([
            'customers' => ['id' => 'cus_test_123', 'object' => 'customer'],
            'checkout/sessions' => [
                'id' => 'cs_test_123',
                'object' => 'checkout.session',
                'url' => 'https://checkout.stripe.test/cs_test_123',
            ],
        ]);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/billing/checkout', ['plan' => 'monthly'])
            ->assertRedirect('https://checkout.stripe.test/cs_test_123');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'stripe_id' => 'cus_test_123',
        ]);
    }

    public function test_checkout_errors_when_the_plan_price_is_not_configured(): void
    {
        config(['billing.plans.monthly.stripe_price' => null]);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/billing/checkout', ['plan' => 'monthly'])
            ->assertRedirect('/billing')
            ->assertSessionHas('error');
    }

    public function test_subscribed_user_is_sent_to_the_portal_instead_of_checkout(): void
    {
        $user = User::factory()->create();
        $this->subscriptionFor($user);

        $this->actingAs($user)
            ->post('/billing/checkout', ['plan' => 'monthly'])
            ->assertRedirect('/billing/portal');
    }

    public function test_billing_portal_redirects_the_user_to_stripe(): void
    {
        $this->fakeStripe([
            'customers' => ['id' => 'cus_test_123', 'object' => 'customer'],
            'billing_portal/sessions' => [
                'id' => 'bps_test_123',
                'object' => 'billing_portal.session',
                'url' => 'https://billing.stripe.test/session',
            ],
        ]);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/billing/portal')
            ->assertRedirect('https://billing.stripe.test/session');
    }
}
