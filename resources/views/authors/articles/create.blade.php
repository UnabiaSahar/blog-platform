<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0">{{ __('Create Article') }}</h2>
    </x-slot>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        @include('authors.articles.form', ['article' => $article, 'route' => route('authors.articles.store')])
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>