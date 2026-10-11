<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscribersOnlyArticleTest extends TestCase
{
    use RefreshDatabase;

    private function article(): Article
    {
        return Article::factory()->published()->create([
            'is_subscribers_only' => true,
            'body' => 'SECRET_BODY_MARKER',
        ]);
    }

    public function test_guest_cannot_read_the_body_of_a_subscribers_only_article(): void
    {
        $article = $this->article();

        $this->get("/articles/{$article->slug}")
            ->assertOk()
            ->assertSee('Subscribe now')
            ->assertDontSee('SECRET_BODY_MARKER');
    }

    public function test_free_user_cannot_read_the_body_of_a_subscribers_only_article(): void
    {
        $reader = User::factory()->create();
        $article = $this->article();

        $this->actingAs($reader)
            ->get("/articles/{$article->slug}")
            ->assertOk()
            ->assertSee('Subscribe now')
            ->assertDontSee('SECRET_BODY_MARKER');
    }

    public function test_active_subscriber_can_read_a_subscribers_only_article(): void
    {
        $subscriber = User::factory()->create();
        $this->subscriptionFor($subscriber);

        $article = $this->article();

        $this->actingAs($subscriber)
            ->get("/articles/{$article->slug}")
            ->assertOk()
            ->assertSee('SECRET_BODY_MARKER')
            ->assertDontSee('Subscribe now');
    }

    public function test_trialing_subscriber_can_read_a_subscribers_only_article(): void
    {
        $subscriber = User::factory()->create();
        $this->subscriptionFor($subscriber, [
            'stripe_status' => 'trialing',
            'trial_ends_at' => now()->addDays(10),
        ]);

        $article = $this->article();

        $this->actingAs($subscriber)
            ->get("/articles/{$article->slug}")
            ->assertOk()
            ->assertSee('SECRET_BODY_MARKER');
    }

    public function test_public_articles_are_readable_by_guests(): void
    {
        $article = Article::factory()->published()->create(['body' => 'PUBLIC_BODY_MARKER']);

        $this->get("/articles/{$article->slug}")
            ->assertOk()
            ->assertSee('PUBLIC_BODY_MARKER');
    }

    public function test_listing_flags_subscribers_only_articles(): void
    {
        $this->article();

        $this->get('/')
            ->assertOk()
            ->assertSee('Subscribers only');
    }
}
