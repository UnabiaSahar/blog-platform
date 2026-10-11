<?php

namespace Database\Seeders;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Seed a mix of draft, published, ad-free and subscribers-only articles.
     */
    public function run(): void
    {
        $author = User::where('email', 'author@example.com')->first();

        if (! $author) {
            return;
        }

        $articles = [
            ['The State of Modern Web Development', ArticleStatus::Published, false, false],
            ['Understanding Laravel Service Containers', ArticleStatus::Published, false, false],
            ['A Practical Guide to Database Indexing', ArticleStatus::Published, false, false],
            ['Writing Tests That Actually Help', ArticleStatus::Published, false, false],
            ['Deep Dive: Queues and Background Jobs', ArticleStatus::Published, true, false],
            ['Scaling a Blog to a Million Readers', ArticleStatus::Published, true, false],
            ['Advanced Caching Strategies', ArticleStatus::Published, false, true],
            ['Behind the Scenes: Our Subscription Roadmap', ArticleStatus::Published, false, true],
            ['Upcoming Features', ArticleStatus::Draft, false, false],
        ];

        foreach ($articles as [$title, $status, $adFree, $subscribersOnly]) {
            Article::factory()
                ->for($author, 'author')
                ->create([
                    'title' => $title,
                    'slug' => Article::uniqueSlug($title),
                    'excerpt' => fake()->paragraph(2),
                    'body' => fake()->paragraphs(6, true),
                    'status' => $status,
                    'ad_free' => $adFree,
                    'is_subscribers_only' => $subscribersOnly,
                    'published_at' => $status === ArticleStatus::Published ? now() : null,
                ]);
        }
    }
}
