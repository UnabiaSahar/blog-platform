<?php

namespace App\Models;

use App\Enums\ArticleStatus;
use Database\Factories\ArticleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable([
    'user_id',
    'title',
    'slug',
    'excerpt',
    'body',
    'cover_image',
    'status',
    'is_subscribers_only',
    'ad_free',
    'published_at',
])]
class Article extends Model
{
    /** @use HasFactory<ArticleFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ArticleStatus::class,
            'is_subscribers_only' => 'boolean',
            'ad_free' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', ArticleStatus::Published)
            ->whereNotNull('published_at');
    }

    /**
     * Generate a unique slug for the given title.
     */
    public static function uniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $base = $slug;
        $suffix = 2;

        while (static::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    /**
     * Publish the article.
     */
    public function publish(): void
    {
        $this->forceFill([
            'status' => ArticleStatus::Published,
            'published_at' => $this->published_at ?? now(),
        ])->save();
    }

    /**
     * Unpublish the article back to a draft.
     */
    public function unpublish(): void
    {
        $this->forceFill([
            'status' => ArticleStatus::Draft,
            'published_at' => null,
        ])->save();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
