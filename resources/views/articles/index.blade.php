<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0">
                {{ __('Latest Articles') }}
            </h2>
            @auth
                @can('create', App\Models\Article::class)
                    <a href="{{ route('authors.articles.create') }}" class="btn btn-primary btn-sm">
                        {{ __('Write Article') }}
                    </a>
                @endcan
            @endauth
        </div>
    </x-slot>

    <div class="container">
        <x-ad-slot placement="listing_top" />

        <div class="row">
            @forelse ($articles as $article)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        @if ($article->cover_image)
                            <img src="{{ asset('storage/'.$article->cover_image) }}" class="card-img-top" alt="{{ $article->title }}" style="height: 180px; object-fit: cover;">
                        @endif
                        <div class="card-body">
                            @if ($article->is_subscribers_only)
                                <span class="badge bg-warning text-dark mb-2">{{ __('Subscribers only') }}</span>
                            @endif
                            <h3 class="h6 fw-bold">
                                <a href="{{ route('articles.show', $article) }}" class="text-decoration-none text-dark">
                                    {{ $article->title }}
                                </a>
                            </h3>
                            <p class="text-muted small mb-0">{{ $article->excerpt }}</p>
                        </div>
                        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                            <small class="text-muted">{{ $article->author->name }}</small>
                            <small class="text-muted">{{ $article->published_at->diffForHumans() }}</small>
                        </div>
                    </div>
                </div>

                @if ($loop->iteration === 3)
                    <div class="col-12">
                        <x-ad-slot placement="in_feed" :article="$article" />
                    </div>
                @endif
            @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        {{ __('No articles published yet. Check back soon!') }}
                    </div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $articles->links() }}
        </div>
    </div>
</x-app-layout>