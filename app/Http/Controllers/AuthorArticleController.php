<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Models\Article;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class AuthorArticleController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('create', Article::class);

        return view('authors.articles.create', [
            'article' => new Article(['status' => 'draft']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArticleRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['cover_image']);

        $article = $request->user()->articles()->create([
            ...$data,
            'slug' => Article::uniqueSlug($data['title']),
            'published_at' => $data['status'] === 'published' ? now() : null,
        ]);

        $this->storeCoverImage($request, $article);

        return redirect()->route('authors.articles.edit', $article)
            ->with('status', 'article-created');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article): View
    {
        Gate::authorize('update', $article);

        return view('authors.articles.edit', ['article' => $article]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArticleRequest $request, Article $article): RedirectResponse
    {
        $data = $request->safe()->except(['cover_image']);

        $article->update([
            ...$data,
            'status' => $data['status'],
            'published_at' => $request->input('status') === 'published'
                ? ($article->published_at ?? now())
                : null,
        ]);

        $this->storeCoverImage($request, $article);

        return redirect()->route('authors.articles.edit', $article)
            ->with('status', 'article-updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article): RedirectResponse
    {
        Gate::authorize('delete', $article);

        if ($article->cover_image) {
            Storage::disk('public')->delete($article->cover_image);
        }

        $article->delete();

        return redirect()->route('dashboard')
            ->with('status', 'article-deleted');
    }

    /**
     * Persist the uploaded cover image when present.
     */
    private function storeCoverImage(StoreArticleRequest|UpdateArticleRequest $request, Article $article): void
    {
        if (! $request->hasFile('cover_image')) {
            return;
        }

        if ($article->cover_image) {
            Storage::disk('public')->delete($article->cover_image);
        }

        $path = $request->file('cover_image')->store('covers', 'public');

        $article->update(['cover_image' => $path]);
    }
}
