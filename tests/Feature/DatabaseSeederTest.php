<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_the_demo_users(): void
    {
        $this->seed();

        $this->assertDatabaseCount('users', 6);
        $this->assertSame(1, User::where('email', 'admin@example.com')->count());
        $this->assertSame(1, User::where('email', 'author@example.com')->count());

        $this->assertTrue(User::where('email', 'admin@example.com')->first()->isAdmin());
        $this->assertTrue(User::where('email', 'author@example.com')->first()->isAuthor());
    }

    public function test_it_seeds_the_expected_subscription_states(): void
    {
        $this->seed();

        $this->assertDatabaseCount('subscriptions', 3);

        $this->assertTrue(User::where('email', 'subscriber@example.com')->first()->hasActiveSubscription());
        $this->assertTrue(User::where('email', 'trialing@example.com')->first()->hasActiveSubscription());
        $this->assertFalse(User::where('email', 'reader@example.com')->first()->hasActiveSubscription());
        $this->assertFalse(User::where('email', 'lapsed@example.com')->first()->hasActiveSubscription());
    }

    public function test_it_seeds_a_mix_of_articles(): void
    {
        $this->seed();

        $this->assertDatabaseCount('articles', 9);
        $this->assertSame(8, Article::published()->count());
        $this->assertSame(1, Article::query()->where('status', 'draft')->count());
        $this->assertSame(2, Article::query()->where('is_subscribers_only', true)->count());
        $this->assertSame(2, Article::query()->where('ad_free', true)->count());
    }
}
