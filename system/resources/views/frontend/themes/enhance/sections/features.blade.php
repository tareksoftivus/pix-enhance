@php
    $d = $section->data ?? [];

    $badgeText = $d['badge_text'] ?? 'Everything included';
    $badgeIcon = $d['badge_icon'] ?? 'ph ph-stack';
    $title = $d['title'] ?? 'One platform for every image problem';
    $titleHighlight = $d['title_highlight'] ?? null;
    $titleSuffix = $d['title_suffix'] ?? '';

    if ($titleHighlight === null && $title === 'One platform for every image problem') {
        $title = 'One platform for every';
        $titleHighlight = 'image problem';
    }

    $subtitle = $d['subtitle'] ?? 'Nine specialised models behind a single, ridiculously simple interface. Upload once, and let PixEnhance decide exactly what your photo needs.';
    $compareBeforeImage = media_url($d['compare_before_image'] ?? null) ?: asset('assets/frontend/enhance/img/samples/valley-before.webp');
    $compareAfterImage = media_url($d['compare_after_image'] ?? null) ?: asset('assets/frontend/enhance/img/samples/valley-after.webp');
    $compareBeforeLabel = $d['compare_before_label'] ?? '1024px';
    $compareAfterLabel = $d['compare_after_label'] ?? '8192px';
    $compareStart = 48;

    $renderIcon = function (?string $icon, string $fallback = ''): string {
        $resolvedIcon = trim($icon ?: $fallback);

        if ($resolvedIcon === '') {
            return '';
        }

        if (str_starts_with($resolvedIcon, 'ph ')) {
            return '<i class="' . e($resolvedIcon) . '"></i>';
        }

        return '<i data-lucide="' . e($resolvedIcon) . '"></i>';
    };

    $fallbackImages = [
        asset('assets/frontend/enhance/img/samples/feature-face.webp'),
        asset('assets/frontend/enhance/img/samples/feature-cutout.webp'),
    ];
    $defaultItems = [
        ['icon' => 'ph ph-user-focus', 'title' => 'Face restoration without identity drift', 'description' => 'Recover eyes, skin texture and old portraits while keeping the person recognisable.', 'label' => 'Identity safe', 'image' => null, 'chip_accent' => false],
        ['icon' => 'ph ph-eraser', 'title' => 'Clean background removal', 'description' => 'Create sharp product cutouts and campaign assets while preserving soft edges and hair detail.', 'label' => 'Transparent PNG', 'image' => null, 'chip_accent' => true],
    ];
    $savedItems = is_array($d['items'] ?? null) ? array_values($d['items']) : [];
    $legacyLeadItem = $savedItems[0] ?? [];
    $hasLegacyLeadItem = ($legacyLeadItem['title'] ?? '') === 'True detail upscaling up to 16K';
    $leadTitle = $d['lead_title'] ?? ($hasLegacyLeadItem ? ($legacyLeadItem['title'] ?? '') : 'True detail upscaling up to 16K');
    $leadDescription = $d['lead_description'] ?? ($hasLegacyLeadItem ? ($legacyLeadItem['description'] ?? '') : 'Most upscalers stretch pixels and call it a day. PixEnhance rebuilds fine texture, lines and colour so tiny images can become useful again.');
    $itemsSource = count($savedItems) > 0 ? ($hasLegacyLeadItem ? array_slice($savedItems, 1) : $savedItems) : $defaultItems;
    $items = collect($itemsSource)
        ->filter(fn (array $item): bool => ! empty($item['title']))
        ->reject(fn (array $item): bool => ($item['title'] ?? null) === 'Batch-ready workflows')
        ->values()
        ->map(fn (array $item, int $index): array => [
            'icon' => $item['icon'] ?? 'sparkles',
            'title' => $item['title'] ?? '',
            'description' => $item['description'] ?? '',
            'label' => $item['label'] ?? '',
            'image_url' => media_url($item['image'] ?? null) ?: ($fallbackImages[$index] ?? null),
            'chip_accent' => (bool) ($item['chip_accent'] ?? false),
        ]);
@endphp

<section class="section" id="features" aria-labelledby="features-title">
    <div class="decor" aria-hidden="true">
        <span class="blob blob-md blob-primary blob-section-right anim-drift"></span>
    </div>

    <div class="shell">
        <header class="section-head" data-reveal="up">
            <span class="badge badge-primary">
                {!! $renderIcon($badgeIcon, 'layers') !!}
                {{ $badgeText }}
            </span>

            <h2 class="text-display-2" id="features-title">
                {{ $title }}
                @if($titleHighlight)
                    <span class="text-gradient anim-gradient">{{ $titleHighlight }}</span>
                @endif
                {{ $titleSuffix }}
            </h2>

            <p class="text-lead">
                {{ $subtitle }}
            </p>
        </header>

        <div class="bento-grid mt-xl" data-reveal="fade">
            <!-- 1 · Lead tile -->
            <article class="card card-hover-glow feature-card feature-card-lead bento-feature" data-reveal="up" data-reveal-delay="1">
                <div class="feature-card__media">
                    <div class="compare" data-compare data-compare-start="{{ $compareStart }}" data-compare-autoplay>
                        <div class="compare__frame">
                            <img class="compare__layer" src="{{ $compareBeforeImage }}"
                                 alt="Mountain valley photograph at low resolution, soft and flat in colour" width="1200" height="750" loading="lazy" decoding="async">
                            <img class="compare__layer compare__layer-after" src="{{ $compareAfterImage }}"
                                 alt="The same valley after AI upscaling, with recovered detail and deeper colour" width="1200" height="750" loading="lazy" decoding="async">
                        </div>

                        <label class="sr-only" for="features-compare">Reveal enhanced landscape</label>
                        <input class="compare__range" type="range" id="features-compare" data-compare-range
                               min="0" max="100" value="{{ $compareStart }}" step="0.1" aria-label="Compare original and upscaled landscape">

                        <span class="compare__tag compare__tag-before">{{ $compareBeforeLabel }}</span>
                        <span class="compare__tag compare__tag-after">
                            {!! $renderIcon('sparkles') !!}
                            {{ $compareAfterLabel }}
                        </span>

                        <span class="compare__handle" aria-hidden="true">
                            <span class="compare__grip">{!! $renderIcon('move-horizontal') !!}</span>
                        </span>
                    </div>
                </div>

                <div class="feature-card__body">
                    <div class="feature-card__head">
                        <span class="feature-card__icon" aria-hidden="true">
                            {!! $renderIcon('ph ph-arrows-out') !!}
                        </span>
                        <h3 class="feature-card__title">{{ $leadTitle }}</h3>
                    </div>

                    <p class="feature-card__text">
                        {{ $leadDescription }}
                    </p>
                </div>
            </article>

            @foreach($items as $item)
                <article class="card card-hover-glow feature-card" data-reveal="up" data-reveal-delay="{{ $loop->iteration + 1 }}">
                    @if(! empty($item['image_url']))
                        <div class="feature-card__media">
                            <img src="{{ $item['image_url'] }}"
                                 alt="{{ $item['title'] }}"
                                 width="900" height="562" loading="lazy" decoding="async">
                            @if(! empty($item['label']))
                                <span class="feature-card__chip @if($item['chip_accent']) feature-card__chip-accent @endif">
                                    {!! $renderIcon($item['icon'] ?? null, 'sparkles') !!}
                                    {{ $item['label'] }}
                                </span>
                            @endif
                        </div>
                    @endif

                    <div class="feature-card__body">
                        <div class="feature-card__head">
                            <span class="feature-card__icon {{ $item['chip_accent'] ? 'feature-card__icon-accent' : 'feature-card__icon-secondary' }}" aria-hidden="true">
                                {!! $renderIcon($item['icon'] ?? null, 'sparkles') !!}
                            </span>
                            <h3 class="feature-card__title">{{ $item['title'] }}</h3>
                        </div>
                        <p class="feature-card__text">{{ $item['description'] ?? '' }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
