<?php

namespace Database\Factories;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(4);

        return [
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => fake()->paragraph(2),
            'body' => fake()->paragraphs(6, true),
            'status' => ArticleStatus::Draft,
            'is_subscribers_only' => false,
            'ad_free' => false,
            'published_at' => null,
        ];
    }

    /**
     * Indicate that the article is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ArticleStatus::Published,
            'published_at' => now(),
        ]);
    }

    /**
     * Indicate that the article is ad-free.
     */
    public function adFree(): static
    {
        return $this->state(fn (array $attributes) => [
            'ad_free' => true,
        ]);
    }
}
