<?php

namespace Tests\Feature;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuthorArticleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_visiting_create_form(): void
    {
        $this->get('/articles/create')
            ->assertRedirect('/login');
    }

    public function test_reader_cannot_access_author_pages(): void
    {
        $reader = User::factory()->create();

        $this->actingAs($reader)
            ->get('/articles/create')
            ->assertForbidden();

        $this->actingAs($reader)
            ->post('/articles', $this->validPayload())
            ->assertForbidden();
    }

    public function test_author_can_create_an_article(): void
    {
        $author = User::factory()->author()->create();
        Storage::fake('public');

        $response = $this->actingAs($author)
            ->post('/articles', [
                'title' => 'My First Article',
                'excerpt' => 'A short excerpt.',
                'body' => 'The article body.',
                'status' => 'draft',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $article = Article::first();

        $this->assertNotNull($article);
        $this->assertSame($author->id, $article->user_id);
        $this->assertSame('my-first-article', $article->slug);
        $this->assertSame(ArticleStatus::Draft, $article->status);
        $this->assertNull($article->published_at);
    }

    public function test_author_can_create_a_published_article(): void
    {
        $author = User::factory()->author()->create();

        $this->actingAs($author)
            ->post('/articles', $this->validPayload(['status' => 'published']))
            ->assertSessionHasNoErrors();

        $article = Article::first();

        $this->assertSame(ArticleStatus::Published, $article->status);
        $this->assertNotNull($article->published_at);
    }

    public function test_article_store_requires_valid_data(): void
    {
        $author = User::factory()->author()->create();

        $this->actingAs($author)
            ->post('/articles', [])
            ->assertSessionHasErrors(['title', 'excerpt', 'body', 'status']);

        $this->assertSame(0, Article::count());
    }

    public function test_duplicate_titles_get_unique_slugs(): void
    {
        $author = User::factory()->author()->create();

        $this->actingAs($author)
            ->post('/articles', $this->validPayload(['title' => 'Same Title']))->assertSessionHasNoErrors();

        $this->actingAs($author)
            ->post('/articles', $this->validPayload(['title' => 'Same Title']))->assertSessionHasNoErrors();

        $this->assertSame('same-title', Article::orderBy('id')->first()->slug);
        $this->assertSame('same-title-2', Article::orderBy('id')->skip(1)->first()->slug);
    }

    public function test_author_can_edit_their_own_article(): void
    {
        $author = User::factory()->author()->create();
        $article = Article::factory()->for($author, 'author')->published()->create();

        $response = $this->actingAs($author)
            ->put("/articles/{$article->slug}", [
                'title' => 'Updated Title',
                'excerpt' => 'Updated excerpt.',
                'body' => 'Updated body.',
                'status' => 'published',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertSame('Updated Title', $article->refresh()->title);
        $this->assertNotNull($article->published_at);
    }

    public function test_author_cannot_edit_another_authors_article(): void
    {
        $owner = User::factory()->author()->create();
        $other = User::factory()->author()->create();
        $article = Article::factory()->for($owner, 'author')->create();

        $this->actingAs($other)
            ->put("/articles/{$article->slug}", $this->validPayload())
            ->assertForbidden();
    }

    public function test_author_can_delete_their_own_article(): void
    {
        $author = User::factory()->author()->create();
        $article = Article::factory()->for($author, 'author')->create();

        $this->actingAs($author)
            ->delete("/articles/{$article->slug}")
            ->assertRedirect(route('dashboard'));

        $this->assertNull($article->fresh());
    }

    public function test_author_cannot_delete_another_authors_article(): void
    {
        $owner = User::factory()->author()->create();
        $other = User::factory()->author()->create();
        $article = Article::factory()->for($owner, 'author')->create();

        $this->actingAs($other)
            ->delete("/articles/{$article->slug}")
            ->assertForbidden();

        $this->assertNotNull($article->fresh());
    }

    public function test_create_page_is_visible_to_author(): void
    {
        $author = User::factory()->author()->create();

        $this->actingAs($author)
            ->get('/articles/create')
            ->assertOk();
    }

    public function test_edit_page_is_visible_to_owner_only(): void
    {
        $owner = User::factory()->author()->create();
        $article = Article::factory()->for($owner, 'author')->create();

        $this->actingAs($owner)->get("/articles/{$article->slug}/edit")->assertOk();

        $stranger = User::factory()->author()->create();
        $this->actingAs($stranger)->get("/articles/{$article->slug}/edit")->assertForbidden();
    }

    /**
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Valid Title',
            'excerpt' => 'A valid excerpt.',
            'body' => 'A valid body.',
            'status' => 'draft',
        ], $overrides);
    }
}
