@php
    $d = $section->data ?? [];

    $badgeText = $d['badge_text'] ?? 'AI routing';
    $badgeIcon = $d['badge_icon'] ?? 'brain';
    $title = $d['title'] ?? 'The right model runs before';
    $titleHighlight = $d['title_highlight'] ?? null;
    $titleSuffix = $d['title_suffix'] ?? '.';

    if ($titleHighlight === null && $title === 'The right model runs before you have to choose it.') {
        $title = 'The right model runs before';
        $titleHighlight = 'you have to choose it';
    }

    $titleHighlight ??= 'you have to choose it';
    $subtitle = $d['subtitle'] ?? 'PixEnhance detects faces, noise, compression, edges and colour problems, then builds the processing path for each image automatically.';
    $primaryButtonText = $d['primary_button_text'] ?? 'Try the upscaler';
    $primaryButtonLink = $d['primary_button_link'] ?? '/upscaler';
    $secondaryButtonText = $d['secondary_button_text'] ?? 'View model docs';
    $secondaryButtonLink = $d['secondary_button_link'] ?? '/docs#models';
    $itemsData = is_array($d['items'] ?? null) && count($d['items']) > 0 ? $d['items'] : [
        ['icon' => 'ph ph-scan', 'title' => 'Scene analysis', 'description' => 'Detects faces, objects, text, noise and blur.'],
        ['icon' => 'ph ph-route', 'title' => 'Adaptive routing', 'description' => 'Sends every image through only the models it needs.'],
        ['icon' => 'ph ph-shield-check', 'title' => 'Quality guards', 'description' => 'Checks identity, edge halos and export readiness.'],
        ['icon' => 'ph ph-download-simple', 'title' => 'Production exports', 'description' => 'Delivers PNG, WebP and large-format outputs for teams.'],
    ];
    $items = collect($itemsData)->filter(fn (array $item): bool => ! empty($item['title']))->values();

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

<section class="section section-surface features-ai" id="features-ai" aria-labelledby="features-ai-title">
    <div class="shell">
        <div class="features-ai__layout">
            <div class="stack stack-md" data-reveal="right">
                <span class="badge badge-primary">
                    {!! $renderIcon($badgeIcon, 'brain') !!}
                    {{ $badgeText }}
                </span>

                <h2 class="text-display-2" id="features-ai-title">
                    {{ $title }}
                    @if($titleHighlight)
                        <span class="text-gradient anim-gradient">{{ $titleHighlight }}</span>@endif{{ $titleSuffix }}
                </h2>

                <p class="text-lead">
                    {{ $subtitle }}
                </p>

                <div class="cluster">
                    <a class="btn btn-primary btn-arrow" href="{{ $primaryButtonLink }}" data-ripple>
                        {{ $primaryButtonText }}
                        <i data-lucide="arrow-right" class="icon-arrow"></i>
                    </a>
                    <a class="btn btn-outline" href="{{ $secondaryButtonLink }}">
                        <i data-lucide="cpu"></i>
                        {{ $secondaryButtonText }}
                    </a>
                </div>
            </div>

            <div class="features-ai__stack" data-reveal="left">
                @foreach($items as $item)
                    <article class="model-tile features-ai__tile">
                        <span class="model-tile__icon" aria-hidden="true">
                            {!! $renderIcon($item['icon'] ?? null, 'sparkles') !!}
                        </span>
                        <span>
                            <span class="model-tile__name">{{ $item['title'] }}</span>
                            <span class="model-tile__meta">{{ $item['description'] ?? '' }}</span>
                        </span>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</section>
