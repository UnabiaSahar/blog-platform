<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticleController extends Controller
{
    /**
     * Display a paginated listing of published articles.
     */
    public function index(): View
    {
        $articles = Article::published()
            ->with('author')
            ->latest('published_at')
            ->paginate(12);

        return view('articles.index', ['articles' => $articles]);
    }

    /**
     * Display the specified article. Drafts are only visible to their author.
     */
    public function show(Request $request, Article $article): View
    {
        if ($article->status->isDraft() && $article->user_id !== $request->user()?->id) {
            abort(404);
        }

        return view('articles.show', ['article' => $article]);
    }
}
