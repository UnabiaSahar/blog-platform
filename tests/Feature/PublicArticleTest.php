<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicArticleTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_articles_are_listed_on_home_page(): void
    {
        $published = Article::factory()->published()->create();
        $draft = Article::factory()->create();

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee($published->title);
        $response->assertDontSee($draft->title);
    }

    public function test_published_article_detail_is_visible_to_guests(): void
    {
        $article = Article::factory()->published()->create();

        $this->get("/articles/{$article->slug}")
            ->assertOk()
            ->assertSee($article->title)
            ->assertSee(explode("\n", $article->body)[0]);
    }

    public function test_draft_article_returns_404_for_guests(): void
    {
        $article = Article::factory()->create();

        $this->get("/articles/{$article->slug}")
            ->assertNotFound();
    }

    public function test_draft_article_returns_404_for_other_authors(): void
    {
        $article = Article::factory()->create();

        $otherAuthor = User::factory()->author()->create();

        $this->actingAs($otherAuthor)
            ->get("/articles/{$article->slug}")
            ->assertNotFound();
    }

    public function test_draft_article_is_visible_to_its_author(): void
    {
        $author = User::factory()->author()->create();
        $article = Article::factory()->for($author, 'author')->create();

        $this->actingAs($author)
            ->get("/articles/{$article->slug}")
            ->assertOk()
            ->assertSee($article->title);
    }
}
