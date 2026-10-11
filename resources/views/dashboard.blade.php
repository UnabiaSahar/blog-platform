<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0">{{ __('Dashboard') }}</h2>
    </x-slot>

    <div class="container">
        <div class="card shadow-sm mb-4">
            <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h3 class="h5 mb-1">{{ __('Your plan') }}</h3>

                    @if ($subscription && $subscription->valid())
                        <span class="badge bg-success">
                            {{ $subscription->onTrial() ? __('Trial') : __('Subscriber') }}
                        </span>

                        @if ($subscription->onGracePeriod())
                            <small class="text-muted ms-2">
                                {{ __('Access ends :date', ['date' => $subscription->ends_at->toFormattedDateString()]) }}
                            </small>
                        @endif
                    @else
                        <span class="badge bg-secondary">{{ __('Free') }}</span>
                        <small class="text-muted ms-2">{{ __('You are seeing ads.') }}</small>
                    @endif
                </div>

                <a href="{{ route('billing.index') }}" class="btn btn-outline-primary btn-sm">
                    {{ $subscription && $subscription->valid() ? __('Manage billing') : __('Upgrade') }}
                </a>
            </div>
        </div>

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
        @endif
    </div>
</x-app-layout>
