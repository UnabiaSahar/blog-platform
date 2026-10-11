<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthorArticleController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ArticleController::class, 'index'])->name('home');

Route::middleware(['auth', 'verified', 'author'])->prefix('articles')->name('authors.articles.')->group(function () {
    Route::get('create', [AuthorArticleController::class, 'create'])->name('create');
    Route::post('/', [AuthorArticleController::class, 'store'])->name('store');
    Route::get('{article}/edit', [AuthorArticleController::class, 'edit'])->name('edit');
    Route::put('{article}', [AuthorArticleController::class, 'update'])->name('update');
    Route::delete('{article}', [AuthorArticleController::class, 'destroy'])->name('destroy');
});

Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show');

Route::middleware(['auth', 'verified'])->get('/dashboard', [DashboardController::class, 'show'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->prefix('billing')->name('billing.')->group(function () {
    Route::get('/', [BillingController::class, 'index'])->name('index');
    Route::post('/checkout', [BillingController::class, 'checkout'])->name('checkout');
    Route::get('/portal', [BillingController::class, 'portal'])->name('portal');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
