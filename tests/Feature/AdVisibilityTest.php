<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdVisibilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.ads.enabled' => true]);
        config(['services.ads.client' => 'ca-pub-test']);
        config(['services.ads.slots.article_top' => '1234567890']);
    }

    public function test_article_renders_ad_for_anonymous_visitor(): void
    {
        $article = Article::factory()->published()->create();

        $this->get("/articles/{$article->slug}")
            ->assertOk()
            ->assertSee('ca-pub-test', false);
    }

    public function test_article_renders_ad_for_free_account_user(): void
    {
        $reader = User::factory()->create();
        $article = Article::factory()->published()->create();

        $this->actingAs($reader)
            ->get("/articles/{$article->slug}")
            ->assertOk()
            ->assertSee('ca-pub-test', false);
    }

    public function test_ad_free_article_renders_no_ad(): void
    {
        $article = Article::factory()->published()->adFree()->create();

        $this->get("/articles/{$article->slug}")
            ->assertOk()
            ->assertDontSee('ca-pub-test', false);
    }

    public function test_listing_renders_listing_top_ad(): void
    {
        Article::factory()->published()->count(5)->create();

        $this->get('/')
            ->assertOk()
            ->assertSee('data-ad-placement="listing_top"', false);
    }

    public function test_article_detail_renders_article_top_and_bottom_ads(): void
    {
        $article = Article::factory()->published()->create();

        $this->get("/articles/{$article->slug}")
            ->assertOk()
            ->assertSee('data-ad-placement="article_top"', false)
            ->assertSee('data-ad-placement="article_bottom"', false);
    }

    public function test_no_ad_when_feature_disabled(): void
    {
        config(['services.ads.enabled' => false]);

        $article = Article::factory()->published()->create();

        $this->get("/articles/{$article->slug}")
            ->assertOk()
            ->assertDontSee('ca-pub-test', false);
    }
}
