@php
    $d = $section->data ?? [];

    $badgeText = $d['badge_text'] ?? 'Enhancement suite';
    $badgeIcon = $d['badge_icon'] ?? 'sparkles';
    $title = $d['title'] ?? 'Features built for every';
    $titleHighlight = $d['title_highlight'] ?? null;
    $titleSuffix = $d['title_suffix'] ?? '.';

    if ($titleHighlight === null && $title === 'Features built for every image workflow.') {
        $title = 'Features built for every';
        $titleHighlight = 'image workflow';
    }

    $titleHighlight ??= 'image workflow';
    $subtitle = $d['subtitle'] ?? 'Upscale, restore, clean, recolour and prepare production-ready images from one focused workspace.';
    $primaryButtonText = $d['primary_button_text'] ?? 'Start free';
    $primaryButtonLink = $d['primary_button_link'] ?? '/register';
    $secondaryButtonText = $d['secondary_button_text'] ?? 'Explore features';
    $secondaryButtonLink = $d['secondary_button_link'] ?? '#features-overview';
    $beforeImageUrl = media_url($d['before_image'] ?? null) ?: asset('assets/frontend/enhance/img/samples/valley-before.webp');
    $afterImageUrl = media_url($d['after_image'] ?? null) ?: asset('assets/frontend/enhance/img/samples/valley-after.webp');
    $beforeLabel = $d['before_label'] ?? 'Original';
    $afterLabel = $d['after_label'] ?? 'Enhanced';
    $metricsData = is_array($d['metrics'] ?? null) && count($d['metrics']) > 0 ? $d['metrics'] : [
        ['value' => '16K', 'label' => 'max output'],
        ['value' => '9', 'label' => 'AI models'],
        ['value' => '2.4s', 'label' => 'avg process'],
    ];
    $metrics = collect($metricsData)->filter(fn (array $metric): bool => ! empty($metric['value']) || ! empty($metric['label']))->values();

    $renderIcon = function (?string $icon, string $fallback = ''): string {
        $resolvedIcon = trim($icon ?: $fallback);

        if ($resolvedIcon === '') {
            return '';
        }

        if (str_starts_with($resolvedIcon, 'ph')) {
            return '<i class="' . e(str_starts_with($resolvedIcon, 'ph ') ? $resolvedIcon : 'ph ' . $resolvedIcon) . '"></i>';
        }

        return '<i data-lucide="' . e($resolvedIcon) . '"></i>';
    };
@endphp

<section class="features-hero" aria-labelledby="features-hero-title">
    <div class="decor" aria-hidden="true">
        <span class="glow-orb glow-orb-hero"></span>
    </div>

    <div class="shell features-hero__inner">
        <div class="features-hero__copy" data-reveal="up">
            <span class="badge badge-primary">
                {!! $renderIcon($badgeIcon, 'sparkles') !!}
                {{ $badgeText }}
            </span>

            <h1 class="text-display-1 features-hero__title" id="features-hero-title">
                {{ $title }}
                @if($titleHighlight)
                    <span class="text-gradient anim-gradient">{{ $titleHighlight }}</span>@endif{{ $titleSuffix }}
            </h1>

            <p class="text-lead features-hero__lead">
                {{ $subtitle }}
            </p>

            <div class="hero__actions">
                <a class="btn btn-primary btn-lg btn-arrow btn-glow" href="{{ url($primaryButtonLink) }}" data-ripple>
                    {{ $primaryButtonText }}
                    <i data-lucide="arrow-right" class="icon-arrow"></i>
                </a>

                <a class="btn btn-secondary btn-lg" href="{{ $secondaryButtonLink }}">
                    {!! $renderIcon('layout-grid') !!}
                    {{ $secondaryButtonText }}
                </a>
            </div>
        </div>

        <div class="features-hero__visual" data-reveal="up" data-reveal-delay="1">
            <div class="compare compare-showcase" data-compare data-compare-start="52" data-compare-autoplay>
                <div class="compare__frame">
                    <img class="compare__layer" src="{{ $beforeImageUrl }}"
                         alt="{{ __('Soft mountain valley photo before AI enhancement') }}"
                         width="1200" height="750" loading="eager" decoding="async">
                    <img class="compare__layer compare__layer-after" src="{{ $afterImageUrl }}"
                         alt="{{ __('Detailed mountain valley photo after AI enhancement') }}"
                         width="1200" height="750" loading="eager" decoding="async">
                </div>

                <label class="sr-only" for="features-hero-compare">{{ __('Reveal enhanced preview') }}</label>
                <input class="compare__range" type="range" id="features-hero-compare" data-compare-range
                       min="0" max="100" value="52" step="0.1" aria-label="{{ __('Compare original and enhanced preview') }}">

                <span class="compare__tag compare__tag-before">{{ $beforeLabel }}</span>
                <span class="compare__tag compare__tag-after">
                    {!! $renderIcon('sparkles') !!}
                    {{ $afterLabel }}
                </span>

                <span class="compare__handle" aria-hidden="true">
                    <span class="compare__grip">{!! $renderIcon('move-horizontal') !!}</span>
                </span>
            </div>

            <div class="features-hero__metrics card card-glass">
                @foreach($metrics as $metric)
                    <div>
                        <strong>{{ $metric['value'] }}</strong>
                        <span>{{ $metric['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
