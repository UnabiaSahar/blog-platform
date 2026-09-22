<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0">{{ __('Edit Article') }}</h2>
            <a href="{{ route('articles.show', $article) }}" class="btn btn-sm btn-outline-secondary">
                {{ __('View Article') }}
            </a>
        </div>
    </x-slot>

    <div class="container">
        @if (session('status') == 'article-updated')
            <div class="alert alert-success">{{ __('Article updated.') }}</div>
        @endif

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        @include('authors.articles.form', ['article' => $article, 'route' => route('authors.articles.update', $article)])
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>