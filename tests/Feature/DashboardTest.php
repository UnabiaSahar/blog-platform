<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_free_reader_sees_the_free_plan_status(): void
    {
        $reader = User::factory()->create();

        $this->actingAs($reader)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Your plan')
            ->assertSee('Free')
            ->assertSee('Upgrade');
    }

    public function test_subscriber_sees_the_subscriber_plan_status(): void
    {
        $reader = User::factory()->create();
        $this->subscriptionFor($reader);

        $this->actingAs($reader)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Your plan')
            ->assertSee('Subscriber')
            ->assertDontSee('Upgrade');
    }

    public function test_author_sees_their_articles(): void
    {
        $author = User::factory()->author()->create();

        $draft = Article::factory()->for($author, 'author')->create(['title' => 'My Draft']);
        $published = Article::factory()->for($author, 'author')->published()->create(['title' => 'My Published']);

        $response = $this->actingAs($author)->get('/dashboard');

        $response->assertOk();
        $response->assertSee($draft->title);
        $response->assertSee($published->title);
    }

    public function test_author_does_not_see_other_authors_articles(): void
    {
        $author = User::factory()->author()->create();
        $other = User::factory()->author()->create();

        Article::factory()->for($other, 'author')->create(['title' => 'Foreign Article']);

        $this->actingAs($author)
            ->get('/dashboard')
            ->assertOk()
            ->assertDontSee('Foreign Article');
    }
}
