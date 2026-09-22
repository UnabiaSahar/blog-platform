@if ($shouldRenderAd())
    <div {{ $attributes->merge(['class' => 'ad-slot text-center my-4']) }} data-ad-placement="{{ $placement }}">
        @if (config('services.ads.client'))
            <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-client="{{ config('services.ads.client') }}"
                 data-ad-slot="{{ config('services.ads.slots.' . $placement) }}"
                 data-ad-format="auto"
                 data-full-width-responsive="true">
            </ins>
            <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
            @pushOnce('ad-scripts')
                <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ config('services.ads.client') }}" crossorigin="anonymous"></script>
            @endpushOnce
        @else
            <div class="border border-dashed border-secondary rounded py-4 text-muted small">
                {{ __('Advertisement') }}
            </div>
        @endif
    </div>
@endif