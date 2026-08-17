@php
    $d = $section->data ?? [];

    $badgeText = $d['badge_text'] ?? 'Cookies';
    $title = $d['title'] ?? 'Cookie Policy';
    $subtitle = $d['subtitle'] ?? 'How PixEnhance uses cookies and similar technologies to keep the product secure, useful and measurable.';
    $panelLabel = $d['panel_label'] ?? 'Last updated';
    $panelTitle = $d['panel_title'] ?? 'August 13, 2026';
    $panelText = $d['panel_text'] ?? 'This policy explains essential, preference and analytics cookies.';
@endphp

<section class="legal-hero" aria-labelledby="cookie-hero-title">
    <div class="decor" aria-hidden="true">
        <span class="glow-orb glow-orb-hero"></span>
    </div>

    <div class="shell legal-hero__inner">
        <div class="legal-hero__copy" data-reveal="up">
            <span class="badge badge-primary">
                <i data-lucide="settings-2"></i>
                {{ $badgeText }}
            </span>

            <h1 class="text-display-1 legal-hero__title" id="cookie-hero-title">
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
