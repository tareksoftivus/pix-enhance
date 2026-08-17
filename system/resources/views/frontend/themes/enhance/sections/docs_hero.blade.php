@php
    $d = $section->data ?? [];

    $badgeText = $d['badge_text'] ?? 'Documentation';
    $badgeIcon = $d['badge_icon'] ?? 'book-open-text';
    $title = $d['title'] ?? 'PixEnhance';
    $titleHighlight = $d['title_highlight'] ?? null;
    $titleSuffix = $d['title_suffix'] ?? '';

    if ($titleHighlight === null && $title === 'PixEnhance Docs') {
        $title = 'PixEnhance';
        $titleHighlight = 'Docs';
    }

    $titleHighlight ??= 'Docs';
    $subtitle = $d['subtitle'] ?? 'Start enhancing images faster with practical setup notes, workflow guidance and API-ready usage patterns.';
    $panelLabel = $d['panel_label'] ?? 'Current guide';
    $panelTitle = $d['panel_title'] ?? 'Image enhancement basics';
    $panelText = $d['panel_text'] ?? 'Covers uploads, models, exports and common production workflows.';

    $renderIcon = function (?string $icon, string $fallback = ''): string {
        $resolvedIcon = trim($icon ?: $fallback);

        if ($resolvedIcon === '') {
            return '';
        }

        if (str_starts_with($resolvedIcon, 'ph-')) {
            $resolvedIcon = 'ph '.$resolvedIcon;
        }

        if (str_starts_with($resolvedIcon, 'ph ')) {
            return '<i class="' . e($resolvedIcon) . '"></i>';
        }

        return '<i data-lucide="' . e($resolvedIcon) . '"></i>';
    };
@endphp

<section class="legal-hero" aria-labelledby="docs-hero-title">
    <div class="decor" aria-hidden="true">
        <span class="glow-orb glow-orb-hero"></span>
    </div>

    <div class="shell legal-hero__inner">
        <div class="legal-hero__copy" data-reveal="up">
            <span class="badge badge-primary">
                {!! $renderIcon($badgeIcon, 'book-open-text') !!}
                {{ $badgeText }}
            </span>

            <h1 class="text-display-1 legal-hero__title" id="docs-hero-title">
                {{ $title }}
                @if($titleHighlight)
                    <span class="text-gradient anim-gradient">{{ $titleHighlight }}</span>@endif{{ $titleSuffix }}
            </h1>

            <p class="text-lead legal-hero__lead">
                {{ $subtitle }}
            </p>
        </div>

        <aside class="legal-hero__panel card card-glass card-pad-lg" data-reveal="up" data-reveal-delay="1">
            <span>{{ $panelLabel }}</span>
            <strong>{{ $panelTitle }}</strong>
            <p>{{ $panelText }}</p>
        </aside>
    </div>
</section>
