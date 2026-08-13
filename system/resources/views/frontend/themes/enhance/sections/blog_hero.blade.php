<section class="blog-hero">
    <div class="grid-overlay" aria-hidden="true"></div>
    <div class="shell blog-hero__inner">
        <div class="blog-hero__copy">
            <p class="text-eyebrow">{{ __('PixEnhance Journal') }}</p>
            <h1 class="text-display-1">
                {{ $activeCategory ? $activeCategory->name : __('Better images, fewer reshoots.') }}
            </h1>
            <p class="text-lead blog-hero__lead">
                {{ $activeCategory
                    ? __('Focused articles from this category, written for teams improving real image pipelines.')
                    : __('Practical notes on upscaling, restoration, background removal and production-ready AI image workflows.') }}
            </p>
        </div>

        <aside class="blog-hero__panel card card-glass card-pad-lg">
            <div class="blog-stat">
                <strong>{{ number_format($posts->total()) }}</strong>
                <span>{{ __('published reads') }}</span>
            </div>
            <div class="blog-stat">
                <strong>{{ $categories->count() }}</strong>
                <span>{{ __('topics') }}</span>
            </div>
            <div class="blog-stat">
                <strong>{{ __('Weekly') }}</strong>
                <span>{{ __('field notes') }}</span>
            </div>
        </aside>
    </div>
</section>
