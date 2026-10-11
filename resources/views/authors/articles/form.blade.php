@php
$isEdit = $article->exists;
@endphp

<form method="POST" action="{{ $route }}" enctype="multipart/form-data">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="mb-3">
        <x-input-label for="title" :value="__('Title')" />
        <x-text-input id="title" name="title" type="text" class="mt-1" :value="old('title', $article->title)" required autofocus />
        <x-input-error :messages="$errors->get('title')" class="mt-2" />
    </div>

    <div class="mb-3">
        <x-input-label for="excerpt" :value="__('Excerpt')" />
        <textarea id="excerpt" name="excerpt" rows="2" class="form-control mt-1" required>{{ old('excerpt', $article->excerpt) }}</textarea>
        <x-input-error :messages="$errors->get('excerpt')" class="mt-2" />
    </div>

    <div class="mb-3">
        <x-input-label for="body" :value="__('Body')" />
        <textarea id="body" name="body" rows="12" class="form-control mt-1" required>{{ old('body', $article->body) }}</textarea>
        <x-input-error :messages="$errors->get('body')" class="mt-2" />
    </div>

    <div class="mb-3">
        <x-input-label for="title" :value="__('Cover Image')" />
        <input id="cover_image" name="cover_image" type="file" class="form-control mt-1" accept="image/*">

        @if ($article->cover_image)
            <img src="{{ asset('storage/'.$article->cover_image) }}" class="img-fluid rounded mt-3" style="max-height: 200px;" alt="">
        @endif

        <x-input-error :messages="$errors->get('cover_image')" class="mt-2" />
    </div>

    <div class="mb-3">
        <x-input-label for="status" :value="__('Status')" />
        <select id="status" name="status" class="form-select mt-1" required>
            <option value="draft" @selected(old('status', $article->status?->value) === 'draft')>{{ __('Draft') }}</option>
            <option value="published" @selected(old('status', $article->status?->value) === 'published')>{{ __('Published') }}</option>
        </select>
        <x-input-error :messages="$errors->get('status')" class="mt-2" />
    </div>

    <div class="form-check mb-4">
        <input id="ad_free" name="ad_free" type="checkbox" value="1" class="form-check-input" @checked((int) old('ad_free', $article->ad_free))>
        <label for="ad_free" class="form-check-label">
            {{ __('Hide ads on this article (goodwill / marketing)') }}
        </label>
        <x-input-error :messages="$errors->get('ad_free')" class="mt-2" />
    </div>

    <div class="d-flex align-items-center gap-2">
        <x-primary-button>
            {{ $isEdit ? __('Save Changes') : __('Create Article') }}
        </x-primary-button>

        @if ($isEdit)
            <button type="submit" form="delete-article" class="btn btn-outline-danger">
                {{ __('Delete') }}
            </button>
        @endif
    </div>
</form>

@if ($isEdit)
    <form id="delete-article" method="POST" action="{{ route('authors.articles.destroy', $article) }}" class="mt-0" onsubmit="return confirm('{{ __('Are you sure you want to delete this article?') }}');">
        @csrf
        @method('DELETE')
    </form>
@endif