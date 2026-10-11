<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionAdSuppressionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.ads.enabled' => true,
            'services.ads.client' => 'ca-pub-test',
            'services.ads.slots.article_top' => '1234567890',
        ]);
    }

    public function test_free_user_has_no_active_subscription(): void
    {
        $user = User::factory()->create();

        $this->assertFalse($user->hasActiveSubscription());
        $this->assertTrue($user->canViewAds());
    }

    public function test_active_subscriber_has_an_active_subscription(): void
    {
        $user = User::factory()->create();
        $this->subscriptionFor($user);

        $this->assertTrue($user->hasActiveSubscription());
        $this->assertFalse($user->canViewAds());
    }

    public function test_trialing_subscriber_has_an_active_subscription(): void
    {
        $user = User::factory()->create();
        $this->subscriptionFor($user, [
            'stripe_status' => 'trialing',
            'trial_ends_at' => now()->addDays(10),
        ]);

        $this->assertTrue($user->hasActiveSubscription());
    }

    public function test_canceled_subscriber_keeps_access_during_the_grace_period(): void
    {
        $user = User::factory()->create();
        $this->subscriptionFor($user, ['ends_at' => now()->addDays(5)]);

        $this->assertTrue($user->hasActiveSubscription());
        $this->assertFalse($user->canViewAds());
    }

    public function test_subscriber_loses_access_after_the_grace_period_ends(): void
    {
        $user = User::factory()->create();
        $this->subscriptionFor($user, ['ends_at' => now()->addDays(5)]);

        $this->travel(6)->days();

        $this->assertFalse($user->hasActiveSubscription());
        $this->assertTrue($user->canViewAds());
    }

    public function test_active_subscriber_does_not_see_ads_on_articles(): void
    {
        $subscriber = User::factory()->create();
        $this->subscriptionFor($subscriber);

        $article = Article::factory()->published()->create();

        $this->actingAs($subscriber)
            ->get("/articles/{$article->slug}")
            ->assertOk()
            ->assertDontSee('ca-pub-test', false);
    }

    public function test_ads_return_once_the_grace_period_ends(): void
    {
        $subscriber = User::factory()->create();
        $this->subscriptionFor($subscriber, ['ends_at' => now()->addDays(5)]);

        $article = Article::factory()->published()->create();

        $this->travel(6)->days();

        $this->actingAs($subscriber)
            ->get("/articles/{$article->slug}")
            ->assertOk()
            ->assertSee('ca-pub-test', false);
    }
}
