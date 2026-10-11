<x-app-layout>
    <x-slot name="header">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Articles') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $article->title }}</li>
            </ol>
        </nav>
    </x-slot>

    <div class="container">
        <article class="bg-white shadow-sm rounded p-4 p-lg-5">
            @if ($article->cover_image)
                <img src="{{ asset('storage/'.$article->cover_image) }}" class="img-fluid rounded mb-4" alt="{{ $article->title }}">
            @endif

            <h1 class="h2 fw-bold">{{ $article->title }}</h1>

            <div class="text-muted mb-4">
                {{ __('By') }} {{ $article->author->name }}
                @if ($article->published_at)
                    &middot; {{ $article->published_at->format('M j, Y') }}
                @endif
            </div>

            <x-ad-slot placement="article_top" :article="$article" />

            <div class="lead fst-italic mb-3">
                {{ $article->excerpt }}
            </div>

            @if ($canRead)
                <div class="article-body">
                    {!! nl2br(e($article->body)) !!}
                </div>

                <x-ad-slot placement="article_bottom" :article="$article" />
            @else
                <div class="alert alert-primary text-center my-4" role="alert">
                    <h2 class="h4">{{ __('This article is for subscribers') }}</h2>
                    <p class="mb-3">{{ __('Subscribe to read the full story and enjoy unlimited ad-free reading.') }}</p>
                    <a href="{{ route('billing.index') }}" class="btn btn-primary">
                        {{ __('Subscribe now') }}
                    </a>
                </div>
            @endif

            @if ($article->user_id === auth()->id())
                <div class="mt-4 d-flex gap-2">
                    <a href="{{ route('authors.articles.edit', $article) }}" class="btn btn-sm btn-outline-secondary">
                        {{ __('Edit') }}
                    </a>
                </div>
            @endif
        </article>
    </div>
</x-app-layout>