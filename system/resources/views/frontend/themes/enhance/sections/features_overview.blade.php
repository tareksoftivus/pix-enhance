@php
    $d = $section->data ?? [];

    $badgeText = $d['badge_text'] ?? 'Core capabilities';
    $badgeIcon = $d['badge_icon'] ?? 'layers';
    $title = $d['title'] ?? 'One workspace for cleanup,';
    $titleHighlight = $d['title_highlight'] ?? null;
    $titleSuffix = $d['title_suffix'] ?? '.';

    if ($titleHighlight === null && $title === 'One workspace for cleanup, scale and delivery.') {
        $title = 'One workspace for cleanup,';
        $titleHighlight = 'scale and delivery';
    }

    $titleHighlight ??= 'scale and delivery';
    $subtitle = $d['subtitle'] ?? 'Every tool is tuned for fast decisions: upload once, choose an output, and keep quality consistent across the whole batch.';
    $fallbackImages = [
        asset('assets/frontend/enhance/img/samples/feature-face.webp'),
        asset('assets/frontend/enhance/img/samples/feature-cutout.webp'),
    ];
    $defaultItems = [
        ['icon' => 'ph ph-user-focus', 'title' => 'Face restoration', 'description' => 'Recover eyes, skin texture and old scans while keeping the subject recognisable.', 'label' => 'Identity safe', 'image' => null],
        ['icon' => 'ph ph-eraser', 'title' => 'Background removal', 'description' => 'Create clean cutouts for product, portrait and campaign imagery with soft-edge detail preserved.', 'label' => 'Transparent PNG', 'image' => null],
        ['icon' => 'ph ph-arrows-out', 'title' => 'Super resolution', 'description' => 'Upscale images up to 16K with reconstructed texture and cleaner edges.', 'label' => '', 'image' => null],
        ['icon' => 'ph ph-aperture', 'title' => 'Denoise and deblur', 'description' => 'Reduce ISO noise, motion softness and compression artefacts before export.', 'label' => '', 'image' => null],
        ['icon' => 'ph ph-palette', 'title' => 'Colour and exposure', 'description' => 'Recover light, balance colour casts and keep the final image natural.', 'label' => '', 'image' => null],
        ['icon' => 'ph ph-tree-structure', 'title' => 'Batch workflow', 'description' => 'Process high-volume folders with presets, queues and consistent output rules.', 'label' => '', 'image' => null],
    ];
    $itemsData = is_array($d['items'] ?? null) && count($d['items']) > 0 ? $d['items'] : $defaultItems;
    $items = collect($itemsData)
        ->filter(fn (array $item): bool => ! empty($item['title']))
        ->values()
        ->map(fn (array $item, int $index): array => [
            'icon' => $item['icon'] ?? 'sparkles',
            'title' => $item['title'] ?? '',
            'description' => $item['description'] ?? '',
            'label' => $item['label'] ?? '',
            'image_url' => media_url($item['image'] ?? null) ?: ($fallbackImages[$index] ?? null),
        ]);

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

<section class="section features-overview" id="features-overview" aria-labelledby="features-overview-title">
    <div class="shell">
        <header class="section-head" data-reveal="up">
            <span class="badge badge-secondary">
                {!! $renderIcon($badgeIcon, 'layers') !!}
                {{ $badgeText }}
            </span>

            <h2 class="text-display-2" id="features-overview-title">
                {{ $title }}
                @if($titleHighlight)
                    <span class="text-gradient anim-gradient">{{ $titleHighlight }}</span>@endif{{ $titleSuffix }}
            </h2>

            <p class="text-lead">
                {{ $subtitle }}
            </p>
        </header>

        <div class="features-page-grid mt-xl">
            @foreach($items as $item)
                @php
                    $hasImage = ! empty($item['image_url']);
                    $cardClass = $hasImage ? 'card card-hover-glow feature-card features-page-card features-page-card--media' : 'card card-tinted features-mini-card';
                @endphp

                <article class="{{ $cardClass }}" data-reveal="up" data-reveal-delay="{{ min($loop->index, 5) }}">
                    @if($hasImage)
                        <div class="feature-card__media">
                            <img src="{{ $item['image_url'] }}"
                                 alt="{{ $item['title'] }}"
                                 width="900" height="562" loading="lazy" decoding="async">
                            @if(! empty($item['label']))
                                <span class="feature-card__chip {{ $loop->index === 1 ? 'feature-card__chip-accent' : '' }}">
                                    {!! $renderIcon($item['icon'] ?? null, 'sparkles') !!}
                                    {{ $item['label'] }}
                                </span>
                            @endif
                        </div>
                        <div class="feature-card__body">
                            <div class="feature-card__head">
                                <span class="feature-card__icon {{ $loop->index === 1 ? 'feature-card__icon-accent' : '' }}" aria-hidden="true">
                                    {!! $renderIcon($item['icon'] ?? null, 'sparkles') !!}
                                </span>
                                <h3 class="feature-card__title">{{ $item['title'] }}</h3>
                            </div>
                            <p class="feature-card__text">{{ $item['description'] ?? '' }}</p>
                        </div>
                    @else
                        {!! $renderIcon($item['icon'] ?? null, 'sparkles') !!}
                        <h3>{{ $item['title'] }}</h3>
                        <p>{{ $item['description'] ?? '' }}</p>
                    @endif
                </article>
            @endforeach
        </div>
    </div>
</section>
