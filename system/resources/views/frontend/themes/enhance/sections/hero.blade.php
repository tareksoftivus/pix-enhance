@php
    $d = $section->data ?? [];

    $eyebrow = $d['eyebrow'] ?? '4.9/5 from 2,480 creators';
    $title = $d['title'] ?? 'Upscale any image to';
    $highlight = $d['highlight'] ?? '16K';
    $titleSuffix = $d['title_suffix'] ?? 'without losing a pixel';
    $subtitle = $d['subtitle'] ?? 'Rebuild real detail instead of stretching pixels. Upscale, restore and clean up any photo in seconds.';
    $primaryButtonText = $d['primary_button_text'] ?? 'Enhance your first image';
    $primaryButtonLink = $d['primary_button_link'] ?? '/register';
    $secondaryButtonText = $d['secondary_button_text'] ?? 'Watch 60s demo';
    $secondaryButtonLink = $d['secondary_button_link'] ?? '#how-it-works';

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

    $visualFloatingCards = is_array($d['visual_floating_cards'] ?? null) ? $d['visual_floating_cards'] : [
        ['variant' => 'ai', 'position' => 'a', 'icon' => 'scan-search', 'value' => '', 'label' => 'Detail recovered', 'subtitle' => 'Shoreline · foliage · texture', 'accent' => false],
        ['variant' => 'stat', 'position' => 'b', 'icon' => 'trending-up', 'value' => '+412%', 'label' => 'Sharpness gain', 'subtitle' => '', 'accent' => true],
        ['variant' => 'stat', 'position' => 'd', 'icon' => 'crop', 'value' => '300 DPI', 'label' => 'Print ready', 'subtitle' => '', 'accent' => false],
    ];
    $visualAppUrl = $d['visual_app_url'] ?? 'app.pixenhance.com/studio';
    $visualBeforeImage = media_url($d['visual_before_image'] ?? null) ?: asset('assets/frontend/enhance/img/samples/beach-before.webp');
    $visualAfterImage = media_url($d['visual_after_image'] ?? null) ?: asset('assets/frontend/enhance/img/samples/beach-after.webp');
    $visualBeforeAlt = $d['visual_before_alt'] ?? 'Low-resolution beach photograph before AI enhancement';
    $visualAfterAlt = $d['visual_after_alt'] ?? 'The same beach photograph after 4× AI upscaling, with sharper detail and richer colour';
    $visualBeforeLabel = $d['visual_before_label'] ?? 'Before';
    $visualAfterLabel = $d['visual_after_label'] ?? 'After';
    $visualBeforeMeta = $d['visual_before_meta'] ?? '960 × 720';
    $visualAfterMeta = $d['visual_after_meta'] ?? '3840 × 2880';
    $visualCompareHint = $d['visual_compare_hint'] ?? 'Drag to compare';
    $visualPanelTitle = $d['visual_panel_title'] ?? 'Enhance';
    $visualPanelBadge = $d['visual_panel_badge'] ?? 'Auto';
    $visualFileName = $d['visual_file_name'] ?? 'beach-cove.jpg';
    $visualFileMeta = $d['visual_file_meta'] ?? '2.4 MB · 960 × 720';
    $visualModelLabel = $d['visual_model_label'] ?? 'Model';
    $visualModelOptions = is_array($d['visual_model_options'] ?? null) ? $d['visual_model_options'] : [
        ['label' => 'Enhance-XL v3', 'selected' => true],
        ['label' => 'Photo Real v2', 'selected' => false],
        ['label' => 'Illustration v1', 'selected' => false],
    ];
    $visualScaleLabel = $d['visual_scale_label'] ?? 'Scale';
    $visualScaleOptions = is_array($d['visual_scale_options'] ?? null) ? $d['visual_scale_options'] : [
        ['label' => '2×', 'value' => '2', 'selected' => false],
        ['label' => '4×', 'value' => '4', 'selected' => true],
        ['label' => '8×', 'value' => '8', 'selected' => false],
    ];
    $visualDetailLabel = $d['visual_detail_label'] ?? 'Detail';
    $visualDetailValue = min(100, max(0, (int) ($d['visual_detail_value'] ?? 72)));
    $visualToggles = is_array($d['visual_toggles'] ?? null) ? $d['visual_toggles'] : [
        ['icon' => 'scan-face', 'label' => 'Face restore', 'name' => 'face', 'enabled' => true],
        ['icon' => 'eraser', 'label' => 'Denoise', 'name' => 'denoise', 'enabled' => true],
        ['icon' => 'palette', 'label' => 'Colour boost', 'name' => 'colour', 'enabled' => true],
    ];
    $visualSubmitText = $d['visual_submit_text'] ?? 'Enhance image';
    $visualRecentLabel = $d['visual_recent_label'] ?? 'Recent';
    $visualThumbnailFallbacks = [
        asset('assets/frontend/enhance/img/samples/thumb-1.webp'),
        asset('assets/frontend/enhance/img/samples/thumb-2.webp'),
        asset('assets/frontend/enhance/img/samples/thumb-3.webp'),
        asset('assets/frontend/enhance/img/samples/thumb-4.webp'),
    ];
    $visualThumbnails = is_array($d['visual_thumbnails'] ?? null) ? $d['visual_thumbnails'] : [
        ['image' => null, 'alt' => '', 'busy' => false],
        ['image' => null, 'alt' => '', 'busy' => false],
        ['image' => null, 'alt' => '', 'busy' => false],
        ['image' => null, 'alt' => '', 'busy' => true],
    ];
@endphp

<section class="hero" aria-labelledby="hero-title">
    <!-- Background decoration layer -->
    <div class="decor" aria-hidden="true">
        <span class="glow-orb glow-orb-hero"></span>
    </div>

    <div class="shell">
        <div class="hero__inner">
            <!-- ============================ COPY ============================ -->
            <div class="hero__copy">
                <div class="hero__review" data-reveal="up">
                    <ul class="avatar-stack avatar-stack-sm">
                        <li><img class="avatar-stack__item"
                                src="{{ asset('assets/frontend/enhance/img/avatars/avatar-1.svg') }}" alt="" width="32"
                                height="32" loading="eager"></li>
                        <li><img class="avatar-stack__item"
                                src="{{ asset('assets/frontend/enhance/img/avatars/avatar-2.svg') }}" alt="" width="32"
                                height="32" loading="eager"></li>
                        <li><img class="avatar-stack__item"
                                src="{{ asset('assets/frontend/enhance/img/avatars/avatar-3.svg') }}" alt="" width="32"
                                height="32" loading="eager"></li>
                        <li><img class="avatar-stack__item"
                                src="{{ asset('assets/frontend/enhance/img/avatars/avatar-4.svg') }}" alt="" width="32"
                                height="32" loading="eager"></li>
                        <li><img class="avatar-stack__item"
                                src="{{ asset('assets/frontend/enhance/img/avatars/avatar-5.svg') }}" alt="" width="32"
                                height="32" loading="eager"></li>
                    </ul>

                    <span class="rating" aria-label="Rated 4.9 out of 5">
                        <i data-lucide="star"></i>
                        <i data-lucide="star"></i>
                        <i data-lucide="star"></i>
                        <i data-lucide="star"></i>
                        <i data-lucide="star"></i>
                    </span>

                    <p class="hero__review-text">
                        {{ $eyebrow }}
                    </p>
                </div>

                <h1 class="text-display-1 hero__title" id="hero-title" data-reveal="up" data-reveal-delay="1">
                    {{ $title }}
                    @if($highlight)
                        <span class="text-gradient anim-gradient">{{ $highlight }}</span>
                    @endif
                    {{ $titleSuffix }}
                </h1>

                <p class="text-lead hero__text" data-reveal="up" data-reveal-delay="2">
                    {{ $subtitle }}
                </p>

                <div class="hero__actions" data-reveal="up" data-reveal-delay="3">
                    <a class="btn btn-primary btn-lg btn-arrow btn-glow" href="{{ url($primaryButtonLink) }}" data-ripple>
                        {{ $primaryButtonText }}
                        <i data-lucide="arrow-right" class="icon-arrow"></i>
                    </a>

                    <a class="btn btn-secondary btn-lg" href="{{ $secondaryButtonLink }}">
                        <i data-lucide="circle-play"></i>
                        {{ $secondaryButtonText }}
                    </a>
                </div>

            </div>

            <!-- =========================== VISUAL =========================== -->
            <div class="hero__visual" data-reveal="zoom" data-reveal-delay="2">
                <div class="hero__stage">
                    <!-- Floating decorative cards -->
                    @foreach($visualFloatingCards as $card)
                        @php
                            $cardPosition = in_array($card['position'] ?? '', ['a', 'b', 'd'], true) ? $card['position'] : 'a';
                            $cardVariant = ($card['variant'] ?? 'stat') === 'ai' ? 'ai' : 'stat';
                            $cardIcon = $card['icon'] ?? ($cardVariant === 'ai' ? 'scan-search' : 'trending-up');
                            $cardValue = $card['value'] ?? '';
                            $cardLabel = $card['label'] ?? '';
                            $cardSubtitle = $card['subtitle'] ?? '';
                            $cardAccentClass = !empty($card['accent']) ? ' stat-chip__icon-accent' : '';
                        @endphp
                        <div class="hero__float hero__float-{{ $cardPosition }} hero__float-hide-sm" aria-hidden="true">
                            @if($cardVariant === 'ai')
                                <div class="ai-chip">
                                    <span class="ai-chip__icon">{!! $renderIcon($cardIcon, 'scan-search') !!}</span>
                                    <span>
                                        @if($cardLabel)
                                            <span class="ai-chip__label">{{ $cardLabel }}</span>
                                        @endif
                                        @if($cardSubtitle)
                                            <span class="ai-chip__sub">{{ $cardSubtitle }}</span>
                                        @endif
                                    </span>
                                </div>
                            @else
                                <div class="stat-chip">
                                    <span class="stat-chip__icon{{ $cardAccentClass }}">{!! $renderIcon($cardIcon, 'trending-up') !!}</span>
                                    <span>
                                        @if($cardValue)
                                            <span class="stat-chip__value">{{ $cardValue }}</span>
                                        @endif
                                        @if($cardLabel)
                                            <span class="stat-chip__label">{{ $cardLabel }}</span>
                                        @endif
                                    </span>
                                </div>
                            @endif
                        </div>
                    @endforeach

                    <!--
                        App dashboard mock.
                        The comparison slider is genuinely interactive; the control rail
                        beside it is a static product illustration built from real form
                        controls, so it is marked inert and hidden from assistive tech.
                    -->
                    <div class="app-mock">
                        <div class="app-mock__bar">
                            <span class="app-mock__dots" aria-hidden="true">
                                <span></span><span></span><span></span>
                            </span>

                            <span class="app-mock__url">
                                {!! $renderIcon('lock') !!}
                                {{ $visualAppUrl }}
                            </span>
                        </div>

                        <div class="app-mock__body">
                            <!-- Preview -->
                            <div class="app-mock__main">
                                <div class="compare compare-hero" data-compare data-compare-start="50"
                                    data-compare-autoplay>
                                    <div class="compare__frame">
                                        <img class="compare__layer"
                                            src="{{ $visualBeforeImage }}"
                                            alt="{{ $visualBeforeAlt }}" width="1200"
                                            height="900" loading="eager" decoding="async" fetchpriority="high">
                                        <img class="compare__layer compare__layer-after"
                                            src="{{ $visualAfterImage }}"
                                            alt="{{ $visualAfterAlt }}"
                                            width="1200" height="900" loading="eager" decoding="async"
                                            fetchpriority="high">
                                    </div>

                                    <label class="sr-only" for="hero-compare">Reveal the enhanced image</label>
                                    <input class="compare__range" type="range" id="hero-compare" data-compare-range
                                        min="0" max="100" value="50" step="0.1"
                                        aria-label="Compare the original and enhanced image">

                                    <span class="compare__tag compare__tag-before">{{ $visualBeforeLabel }}</span>
                                    <span class="compare__tag compare__tag-after">
                                        {!! $renderIcon('sparkles') !!}
                                        {{ $visualAfterLabel }}
                                    </span>

                                    <span class="compare__meta compare__meta-before">{{ $visualBeforeMeta }}</span>
                                    <span class="compare__meta compare__meta-after">{{ $visualAfterMeta }}</span>

                                    <span class="compare__hint">
                                        {!! $renderIcon('move-horizontal') !!}
                                        {{ $visualCompareHint }}
                                    </span>

                                    <span class="compare__handle" aria-hidden="true">
                                        <span class="compare__grip">
                                            {!! $renderIcon('move-horizontal') !!}
                                        </span>
                                    </span>
                                </div>
                            </div>

                            <!-- Control rail — real inputs, presentation only -->
                            <div class="app-mock__side" inert aria-hidden="true">
                                <div class="app-mock__side-head">
                                    <span class="mock-panel__title">
                                        {!! $renderIcon('sliders-horizontal') !!}
                                        {{ $visualPanelTitle }}
                                    </span>
                                    <span class="badge badge-sm badge-primary">{{ $visualPanelBadge }}</span>
                                </div>

                                <label class="file-field">
                                    <input class="file-field__input" type="file" name="source" accept="image/*"
                                        tabindex="-1">
                                    <span class="file-field__icon">{!! $renderIcon('image-plus') !!}</span>
                                    <span class="file-field__body">
                                        <span class="file-field__name">{{ $visualFileName }}</span>
                                        <span class="file-field__meta">{{ $visualFileMeta }}</span>
                                    </span>
                                </label>

                                <div class="rail-row rail-row-stack">
                                    <label class="rail-row__label" for="mock-model">
                                        {!! $renderIcon('cpu') !!}
                                        {{ $visualModelLabel }}
                                    </label>
                                    <select class="select rail-select" id="mock-model" name="model" tabindex="-1">
                                        @foreach($visualModelOptions as $option)
                                            <option @selected(!empty($option['selected']))>{{ $option['label'] ?? '' }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="rail-row">
                                    <span class="rail-row__label">
                                        {!! $renderIcon('maximize-2') !!}
                                        {{ $visualScaleLabel }}
                                    </span>
                                    <span class="radio-group">
                                        @foreach($visualScaleOptions as $option)
                                            @php
                                                $scaleId = 'mock-scale-' . $loop->index;
                                            @endphp
                                            <span class="radio-group__option">
                                                <input class="radio-group__input" type="radio" id="{{ $scaleId }}"
                                                    name="scale" value="{{ $option['value'] ?? $loop->index }}" @checked(!empty($option['selected'])) tabindex="-1">
                                                <label class="radio-group__label" for="{{ $scaleId }}">{{ $option['label'] ?? '' }}</label>
                                            </span>
                                        @endforeach
                                    </span>
                                </div>

                                <div class="rail-row rail-row-stack">
                                    <span class="rail-row cluster-between">
                                        <label class="rail-row__label" for="mock-detail">
                                            {!! $renderIcon('focus') !!}
                                            {{ $visualDetailLabel }}
                                        </label>
                                        <span class="rail-value">{{ $visualDetailValue }}%</span>
                                    </span>
                                    <input class="range rail-range" type="range" id="mock-detail" name="detail" min="0"
                                        max="100" value="{{ $visualDetailValue }}" tabindex="-1">
                                </div>

                                @foreach($visualToggles as $toggle)
                                    @php
                                        $toggleName = $toggle['name'] ?? 'toggle-' . $loop->index;
                                        $toggleId = 'mock-' . \Illuminate\Support\Str::slug($toggleName);
                                    @endphp
                                    <div class="rail-row">
                                        <label class="rail-row__label" for="{{ $toggleId }}">
                                            {!! $renderIcon($toggle['icon'] ?? null, 'check-circle') !!}
                                            {{ $toggle['label'] ?? '' }}
                                        </label>
                                        <span class="switch-field">
                                            <input class="switch-field__input" type="checkbox" id="{{ $toggleId }}"
                                                name="{{ $toggleName }}" @checked(!empty($toggle['enabled'])) tabindex="-1">
                                            <span class="switch-field__track"></span>
                                        </span>
                                    </div>
                                @endforeach

                                <button class="btn btn-primary btn-sm btn-block rail-submit" type="button"
                                    tabindex="-1">
                                    {!! $renderIcon('sparkles') !!}
                                    {{ $visualSubmitText }}
                                </button>
                            </div>
                        </div>

                        <!-- Status bar -->
                        <div class="app-mock__foot">
                            <div class="foot-thumbs" aria-hidden="true">
                                <span class="foot-thumbs__label">{{ $visualRecentLabel }}</span>
                                @foreach($visualThumbnails as $thumbnail)
                                    @php
                                        $thumbnailSrc = media_url($thumbnail['image'] ?? null) ?: ($visualThumbnailFallbacks[$loop->index] ?? $visualThumbnailFallbacks[0]);
                                        $thumbnailAlt = $thumbnail['alt'] ?? '';
                                    @endphp
                                    <span class="mock-thumb @if(!empty($thumbnail['busy'])) mock-thumb-busy @endif">
                                        <img src="{{ $thumbnailSrc }}" alt="{{ $thumbnailAlt }}"
                                            width="320" height="320" loading="lazy">
                                        @if(!empty($thumbnail['busy']))
                                            <span class="scanline"></span>
                                        @endif
                                    </span>
                                @endforeach
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
