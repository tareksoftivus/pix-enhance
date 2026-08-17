@php
    $d = $section->data ?? [];

    $eyebrow = $d['eyebrow'] ?? 'PixEnhance Journal';
    $title = $d['title'] ?? 'Better images,';
    $titleHighlight = $d['title_highlight'] ?? null;
    $titleSuffix = $d['title_suffix'] ?? '.';

    if ($titleHighlight === null && $title === 'Better images, fewer reshoots.') {
        $title = 'Better images,';
        $titleHighlight = 'fewer reshoots';
    }

    $titleHighlight ??= 'fewer reshoots';
    $subtitle = $d['subtitle'] ?? 'Practical notes on upscaling, restoration, background removal and production-ready AI image workflows.';
    $categorySubtitle = $d['category_subtitle'] ?? 'Focused articles from this category, written for teams improving real image pipelines.';
    $postsLabel = $d['posts_label'] ?? 'published reads';
    $categoriesLabel = $d['categories_label'] ?? 'topics';
    $cadenceValue = $d['cadence_value'] ?? 'Weekly';
    $cadenceLabel = $d['cadence_label'] ?? 'field notes';
@endphp

<section class="blog-hero">
    <div class="grid-overlay" aria-hidden="true"></div>
    <div class="shell blog-hero__inner">
        <div class="blog-hero__copy">
            <p class="text-eyebrow">{{ $eyebrow }}</p>
            <h1 class="text-display-1">
                @if($activeCategory)
                    {{ $activeCategory->name }}
                @else
                    {{ $title }}
                    @if($titleHighlight)
                    <span class="text-gradient anim-gradient">{{ $titleHighlight }}</span>@endif{{ $titleSuffix }}
                @endif
            </h1>
            <p class="text-lead blog-hero__lead">
                {{ $activeCategory ? $categorySubtitle : $subtitle }}
            </p>
        </div>

        <aside class="blog-hero__panel card card-glass card-pad-lg">
            <div class="blog-stat">
                <strong>{{ number_format($posts->total()) }}</strong>
                <span>{{ $postsLabel }}</span>
            </div>
            <div class="blog-stat">
                <strong>{{ $categories->count() }}</strong>
                <span>{{ $categoriesLabel }}</span>
            </div>
            <div class="blog-stat">
                <strong>{{ $cadenceValue }}</strong>
                <span>{{ $cadenceLabel }}</span>
            </div>
        </aside>
    </div>
</section>
