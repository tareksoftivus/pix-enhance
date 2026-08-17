@php
    $d = $section->data ?? [];

    $badgeText = $d['badge_text'] ?? 'Privacy';
    $title = $d['title'] ?? 'Privacy Policy';
    $subtitle = $d['subtitle'] ?? 'How PixEnhance collects, uses and protects account, usage and image-processing data.';
    $panelLabel = $d['panel_label'] ?? 'Last updated';
    $panelTitle = $d['panel_title'] ?? 'August 13, 2026';
    $panelText = $d['panel_text'] ?? 'This policy covers website, dashboard and API activity.';
@endphp

<section class="legal-hero" aria-labelledby="privacy-hero-title">
    <div class="decor" aria-hidden="true">
        <span class="glow-orb glow-orb-hero"></span>
    </div>

    <div class="shell legal-hero__inner">
        <div class="legal-hero__copy" data-reveal="up">
            <span class="badge badge-primary">
                <i data-lucide="shield-check"></i>
                {{ $badgeText }}
            </span>

            <h1 class="text-display-1 legal-hero__title" id="privacy-hero-title">
                {{ $title }}
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
