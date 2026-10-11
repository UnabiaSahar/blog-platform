<?php

namespace App\Http\Controllers;

use App\Enums\ArticleStatus;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the reader or author dashboard.
     */
    public function show(Request $request): View
    {
        $user = $request->user();
        $subscription = $user->subscription('default');

        if (! $user->isAuthor()) {
            return view('dashboard', [
                'subscription' => $subscription,
            ]);
        }

        $draftArticles = $user->articles()
            ->where('status', ArticleStatus::Draft)
            ->latest()
            ->get();

        $publishedArticles = $user->articles()
            ->where('status', ArticleStatus::Published)
            ->latest()
            ->get();

        return view('dashboard', [
            'draftArticles' => $draftArticles,
            'publishedArticles' => $publishedArticles,
            'subscription' => $subscription,
        ]);
    }
}
