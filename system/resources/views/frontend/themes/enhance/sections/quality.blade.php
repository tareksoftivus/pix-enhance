@php
    $d = $section->data ?? [];

    $badgeText = ($d['badge_text'] ?? null) === 'Quality check'
        ? 'Image quality comparison'
        : ($d['badge_text'] ?? 'Image quality comparison');
    $badgeIcon = $d['badge_icon'] ?? 'focus';
    $title = ($d['title'] ?? null) === 'See what real detail recovery looks like'
        ? 'Detail that was never'
        : ($d['title'] ?? 'Detail that was never');
    $titleHighlight = $d['title_highlight'] ?? 'in the file';
    $titleSuffix = $d['title_suffix'] ?? '';
    $subtitle = ($d['subtitle'] ?? null) === 'Compare original files against AI-enhanced outputs before you commit credits to a whole batch.'
        ? 'Interpolation just averages the pixels you already have. PixEnhance predicts the detail your lens missed.'
        : ($d['subtitle'] ?? 'Interpolation just averages the pixels you already have. PixEnhance predicts the detail your lens missed.');
    $beforeImageUrl = media_url($d['before_image'] ?? null) ?: asset('assets/frontend/enhance/img/samples/family-before.webp');
    $afterImageUrl = media_url($d['after_image'] ?? null) ?: asset('assets/frontend/enhance/img/samples/family-after.webp');
    $beforeLabel = $d['before_label'] ?? 'Original';
    $afterLabel = $d['after_label'] ?? 'PixEnhance';
    $beforeMeta = $d['before_meta'] ?? 'JPEG · q35 · 512 px';
    $afterMeta = $d['after_meta'] ?? 'PNG · lossless · 4096 px';
    $compareStart = 45;
    $defaultItems = [
        ['title' => 'No plastic skin, no halos', 'description' => 'A perceptual loss function keeps texture believable instead of over-smoothing everything into wax.'],
        ['title' => 'JPEG artefacts removed first', 'description' => 'Blocking and ringing are cleaned before upscaling, so compression damage never gets amplified.'],
        ['title' => 'Text and logos stay sharp', 'description' => 'A dedicated edge pass keeps typography, packaging and UI screenshots crisp and readable.'],
    ];
    $items = is_array($d['items'] ?? null) && count($d['items']) > 0 ? $d['items'] : $defaultItems;
    $qualityItems = collect($items)->filter(fn (array $item): bool => ! empty($item['title']))->values();
    $primaryButtonText = $d['primary_button_text'] ?? 'Try it on your photo';
    $primaryButtonLink = $d['primary_button_link'] ?? '/upscaler';
    $secondaryButtonText = $d['secondary_button_text'] ?? 'Compare models';
    $secondaryButtonLink = $d['secondary_button_link'] ?? '/docs#models';

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
@endphp

<section class="section" id="quality" aria-labelledby="quality-title">
    <div class="decor" aria-hidden="true">
        <span class="blob blob-md blob-secondary blob-section-left anim-drift"></span>
    </div>

    <div class="shell">
        <div class="showcase showcase-media-lead">
            <!-- Copy -->
            <div class="stack stack-md" data-reveal="right">
                <span class="badge badge-primary">
                    {!! $renderIcon($badgeIcon, 'focus') !!}
                    {{ $badgeText }}
                </span>

                <h2 class="text-display-2" id="quality-title">
                    {{ $title }}
                    @if($titleHighlight)
                        <span class="text-gradient anim-gradient">{{ $titleHighlight }}</span>
                    @endif
                    {{ $titleSuffix }}
                </h2>

                <p class="text-lead">
                    {{ $subtitle }}
                </p>

                <ul class="showcase__list">
                    @foreach($qualityItems as $item)
                        <li class="showcase__list-item">
                            <span class="showcase__list-icon" aria-hidden="true">{!! $renderIcon('check') !!}</span>
                            <span>
                                <strong>{{ $item['title'] }}</strong>
                                {{ $item['description'] ?? '' }}
                            </span>
                        </li>
                    @endforeach
                </ul>

                <div class="cluster">
                    <a class="btn btn-primary btn-arrow" href="{{ $primaryButtonLink }}" data-ripple>
                        {{ $primaryButtonText }}
                        <i data-lucide="arrow-right" class="icon-arrow"></i>
                    </a>
                    <a class="btn btn-outline" href="{{ $secondaryButtonLink }}">
                        {!! $renderIcon('cpu') !!}
                        {{ $secondaryButtonText }}
                    </a>
                </div>
            </div>

            <!-- Media -->
            <div class="compare compare-showcase" data-reveal="left" data-compare data-compare-start="{{ $compareStart }}" data-compare-autoplay>
                <div class="compare__frame">
                    <img class="compare__layer" src="{{ $beforeImageUrl }}"
                         alt="A 1920s family portrait as a low-resolution scan — soft, faded and marked by compression"
                         width="1300" height="1500" loading="lazy" decoding="async">
                    <img class="compare__layer compare__layer-after" src="{{ $afterImageUrl }}"
                         alt="The same portrait after AI restoration, with recovered facial detail, fabric texture and warmth"
                         width="1300" height="1500" loading="lazy" decoding="async">
                </div>

                <label class="sr-only" for="quality-compare">Reveal the enhanced photo</label>
                <input class="compare__range" type="range" id="quality-compare" data-compare-range
                       min="0" max="100" value="{{ $compareStart }}" step="0.1" aria-label="Compare the original and enhanced photo">

                <span class="compare__tag compare__tag-before">{{ $beforeLabel }}</span>
                <span class="compare__tag compare__tag-after">
                    {!! $renderIcon('sparkles') !!}
                    {{ $afterLabel }}
                </span>

                <span class="compare__meta compare__meta-before">{{ $beforeMeta }}</span>
                <span class="compare__meta compare__meta-after">{{ $afterMeta }}</span>

                <span class="compare__handle" aria-hidden="true">
                    <span class="compare__grip">{!! $renderIcon('move-horizontal') !!}</span>
                </span>
            </div>
        </div>
    </div>
</section>
