<?php

namespace App\View\Components;

use App\Models\Article;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AdSlot extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $placement,
        public ?Article $article = null,
    ) {}

    /**
     * Determine whether an ad should be rendered.
     */
    public function shouldRenderAd(): bool
    {
        if (! config('services.ads.enabled')) {
            return false;
        }

        if ($this->article?->ad_free) {
            return false;
        }

        if (auth()->check() && auth()->user()->canViewAds() === false) {
            return false;
        }

        return true;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.ad-slot');
    }
}
