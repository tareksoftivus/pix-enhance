@php
    $d = $section->data ?? [];

    $badgeText = ($d['badge_text'] ?? null) === 'Workflow'
        ? 'From upload to download in seconds'
        : ($d['badge_text'] ?? 'From upload to download in seconds');
    $badgeIcon = $d['badge_icon'] ?? 'rocket';
    $title = ($d['title'] ?? null) === 'Three steps from low-res to launch-ready'
        ? 'How PixEnhance works'
        : ($d['title'] ?? 'How PixEnhance works');
    $subtitle = ($d['subtitle'] ?? null) === 'Upload an image, let the AI route the work, then export exactly what your channel needs.'
        ? 'No masks, no layers, no plugin installs. Three steps — and the fourth one is simply admiring the result.'
        : ($d['subtitle'] ?? 'No masks, no layers, no plugin installs. Three steps — and the fourth one is simply admiring the result.');

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

    $defaultSteps = [
        ['icon' => 'cloud-upload', 'title' => 'Upload', 'description' => 'Drag in a single photo or 500 at once. JPG, PNG, WEBP, AVIF, HEIC and TIFF up to 50 MB each.'],
        ['icon' => 'brain', 'title' => 'Auto-analyse', 'description' => 'PixEnhance inspects noise, blur, faces and compression, then picks the right model stack for your image.'],
        ['icon' => 'wand-sparkles', 'title' => 'Enhance', 'description' => 'Detail is reconstructed on dedicated GPUs — typically 2.4 seconds for a 4× upscale, with live progress.'],
        ['icon' => 'download', 'title' => 'Download', 'description' => 'Grab a lossless PNG, TIFF or optimised WEBP — or pull it straight from the API into your own product.'],
    ];
    $legacyStepIcons = [
        'ph ph-cloud-arrow-up' => 'cloud-upload',
        'ph ph-brain' => 'brain',
        'ph ph-magic-wand' => 'wand-sparkles',
        'ph ph-download-simple' => 'download',
    ];
    $legacyDescriptions = [
        'Drag in a single photo or 500 at once. JPG, PNG, WEBP, AVIF, HEIC and TIFF up to 50 MB each.' => $defaultSteps[0]['description'],
        'PixEnhance inspects noise, blur, faces and compression, then picks the right model stack for your image.' => $defaultSteps[1]['description'],
        'Detail is reconstructed on dedicated GPUs - typically 2.4 seconds for a 4x upscale, with live progress.' => $defaultSteps[2]['description'],
        'Grab a lossless PNG, TIFF or optimised WEBP - or pull it straight from the API into your own product.' => $defaultSteps[3]['description'],
    ];
    $items = is_array($d['items'] ?? null) && count($d['items']) > 0 ? $d['items'] : $defaultSteps;
    $steps = collect($items)
        ->filter(fn (array $item): bool => ! empty($item['title']))
        ->values()
        ->map(fn (array $item): array => [
            'icon' => $legacyStepIcons[$item['icon'] ?? ''] ?? ($item['icon'] ?? 'sparkles'),
            'title' => $item['title'] ?? '',
            'description' => $legacyDescriptions[$item['description'] ?? ''] ?? ($item['description'] ?? ''),
        ]);

    $defaultStats = [
        ['value' => '48200000', 'label' => 'Images enhanced', 'suffix' => '+', 'decimals' => 0, 'compact' => true, 'gradient' => true],
        ['value' => '2.4', 'label' => 'Average render time', 'suffix' => 's', 'decimals' => 1, 'compact' => false, 'gradient' => false],
        ['value' => '99.98', 'label' => 'Uptime last 12 months', 'suffix' => '%', 'decimals' => 2, 'compact' => false, 'gradient' => false],
        ['value' => '9400', 'label' => 'Active creators', 'suffix' => '+', 'decimals' => 0, 'compact' => false, 'gradient' => false],
    ];
    $statsData = is_array($d['stats'] ?? null) && count($d['stats']) > 0 ? $d['stats'] : $defaultStats;
    $stats = collect($statsData)
        ->filter(fn (array $stat): bool => ! empty($stat['value']) || ! empty($stat['label']))
        ->values()
        ->map(fn (array $stat, int $index): array => [
            'value' => $stat['value'] ?? 0,
            'label' => $stat['label'] ?? '',
            'suffix' => $stat['suffix'] ?? '',
            'decimals' => (int) ($stat['decimals'] ?? 0),
            'compact' => array_key_exists('compact', $stat) ? (bool) $stat['compact'] : $index === 0,
            'gradient' => array_key_exists('gradient', $stat) ? (bool) $stat['gradient'] : $index === 0,
        ]);
@endphp

<section class="section section-surface" id="how-it-works" aria-labelledby="how-title">
    <div class="shell">
        <header class="section-head" data-reveal="up">
            <span class="badge badge-secondary">
                {!! $renderIcon($badgeIcon, 'rocket') !!}
                {{ $badgeText }}
            </span>

            <h2 class="text-display-2" id="how-title">{{ $title }}</h2>

            <p class="text-lead">
                {{ $subtitle }}
            </p>
        </header>

        <ol class="steps steps-4 mt-xl">
            @foreach($steps as $step)
                <li class="step" data-reveal="up" data-reveal-delay="{{ min($loop->iteration, 4) }}">
                    <span class="step__badge" aria-hidden="true">
                        {!! $renderIcon($step['icon'] ?? null, 'sparkles') !!}
                        <span class="step__number">{{ $loop->iteration }}</span>
                    </span>
                    <h3 class="step__title">{{ $step['title'] }}</h3>
                    <p class="step__text">{{ $step['description'] ?? '' }}</p>
                </li>
            @endforeach
        </ol>

        <!-- Live stats -->
        <div class="stat-grid stat-grid-4 mt-xl" data-reveal="up">
            @foreach($stats as $stat)
                <div class="stat">
                    <span class="stat__value @if($stat['gradient']) stat__value-gradient @endif"
                          data-counter="{{ $stat['value'] ?? 0 }}"
                          @if($stat['compact']) data-counter-compact @endif
                          @if($stat['decimals'] > 0) data-counter-decimals="{{ $stat['decimals'] }}" @endif
                          @if($stat['suffix'] !== '') data-counter-suffix="{{ $stat['suffix'] }}" @endif>0</span>
                    <span class="stat__label">{{ $stat['label'] ?? '' }}</span>
                </div>
            @endforeach
        </div>
    </div>
</section>
