@php
    $d = $section->data ?? [];

    $badgeText = $d['badge_text'] ?? 'Simple credit pricing';
    $badgeIcon = $d['badge_icon'] ?? 'gem';
    $title = $d['title'] ?? 'Pricing that scales with your';
    $titleHighlight = $d['title_highlight'] ?? null;
    $titleSuffix = $d['title_suffix'] ?? '.';

    if ($titleHighlight === null && $title === 'Pricing that scales with your image pipeline.') {
        $title = 'Pricing that scales with your';
        $titleHighlight = 'image pipeline';
    }

    $titleHighlight ??= 'image pipeline';
    $subtitle = $d['subtitle'] ?? 'Start free, upgrade when volume grows, and keep every enhancement feature available on every paid plan.';
    $primaryButtonText = $d['primary_button_text'] ?? 'Start free';
    $primaryButtonLink = $d['primary_button_link'] ?? '/register';
    $secondaryButtonText = $d['secondary_button_text'] ?? 'Compare plans';
    $secondaryButtonLink = $d['secondary_button_link'] ?? '#pricing-compare';
    $metricsData = is_array($d['metrics'] ?? null) && count($d['metrics']) > 0 ? $d['metrics'] : [
        ['label' => 'Free start', 'value' => '10 credits'],
        ['label' => 'Paid plans from', 'value' => '$19'],
        ['label' => 'Maximum output', 'value' => '16K'],
    ];
    $benefitsData = is_array($d['benefits'] ?? null) && count($d['benefits']) > 0 ? $d['benefits'] : [
        ['text' => 'All AI models included'],
        ['text' => 'Cancel or change plans anytime'],
        ['text' => 'Commercial licence on paid plans'],
    ];
    $metrics = collect($metricsData)->filter(fn (array $metric): bool => ! empty($metric['label']) || ! empty($metric['value']))->values();
    $benefits = collect($benefitsData)->filter(fn (array $benefit): bool => ! empty($benefit['text']))->values();

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

<section class="pricing-hero" aria-labelledby="pricing-hero-title">
    <div class="decor" aria-hidden="true">
        <span class="glow-orb glow-orb-hero"></span>
    </div>

    <div class="shell pricing-hero__inner">
        <div class="pricing-hero__copy" data-reveal="up">
            <span class="badge badge-primary">
                {!! $renderIcon($badgeIcon, 'gem') !!}
                {{ $badgeText }}
            </span>

            <h1 class="text-display-1 pricing-hero__title" id="pricing-hero-title">
                {{ $title }}
                @if($titleHighlight)
                    <span class="text-gradient anim-gradient">{{ $titleHighlight }}</span>@endif{{ $titleSuffix }}
            </h1>

            <p class="text-lead pricing-hero__lead">
                {{ $subtitle }}
            </p>

            <div class="hero__actions">
                <a class="btn btn-primary btn-lg btn-arrow btn-glow" href="{{ url($primaryButtonLink) }}" data-ripple>
                    {{ $primaryButtonText }}
                    <i data-lucide="arrow-right" class="icon-arrow"></i>
                </a>

                <a class="btn btn-secondary btn-lg" href="{{ $secondaryButtonLink }}">
                    {!! $renderIcon('table-2') !!}
                    {{ $secondaryButtonText }}
                </a>
            </div>
        </div>

        <aside class="pricing-hero__panel card card-glass card-pad-lg" data-reveal="up" data-reveal-delay="1">
            @foreach($metrics as $metric)
                <div class="pricing-hero__metric">
                    <span>{{ $metric['label'] ?? '' }}</span>
                    <strong>{{ $metric['value'] ?? '' }}</strong>
                </div>
            @endforeach

            <ul class="pricing-hero__list">
                @foreach($benefits as $benefit)
                    <li>{!! $renderIcon('check') !!}{{ $benefit['text'] ?? '' }}</li>
                @endforeach
            </ul>
        </aside>
    </div>
</section>
