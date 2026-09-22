<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0">{{ __('Dashboard') }}</h2>
    </x-slot>

    <div class="container">
        @if (auth()->user()->isAuthor())
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-white fw-semibold">
                            {{ __('Drafts') }}
                        </div>
                        <div class="card-body">
                            @forelse ($draftArticles as $article)
                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <a href="{{ route('authors.articles.edit', $article) }}" class="text-decoration-none">
                                        {{ $article->title }}
                                    </a>
                                    <small class="text-muted">{{ $article->updated_at->diffForHumans() }}</small>
                                </div>
                            @empty
                                <p class="text-muted mb-0">{{ __('No drafts yet.') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-white fw-semibold">
                            {{ __('Published') }}
                        </div>
                        <div class="card-body">
                            @forelse ($publishedArticles as $article)
                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <a href="{{ route('articles.show', $article) }}" class="text-decoration-none">
                                        {{ $article->title }}
                                    </a>
                                    <small class="text-muted">{{ $article->published_at->diffForHumans() }}</small>
                                </div>
                            @empty
                                <p class="text-muted mb-0">{{ __('Nothing published yet.') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <a href="{{ route('authors.articles.create') }}" class="btn btn-primary">
                {{ __('Write New Article') }}
            </a>
        @else
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="h5">{{ __('Welcome, :name!', ['name' => auth()->user()->name]) }}</h3>
                    <p class="text-muted mb-0">
                        {{ __("You're on the free plan. Billing and subscriptions are coming soon.") }}
                    </p>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>