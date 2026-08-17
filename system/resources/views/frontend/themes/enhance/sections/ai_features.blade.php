@php
    $d = $section->data ?? [];

    $badgeText = ($d['badge_text'] ?? null) === 'AI model stack'
        ? 'Inside the engine'
        : ($d['badge_text'] ?? 'Inside the engine');
    $badgeIcon = $d['badge_icon'] ?? 'brain';
    $title = ($d['title'] ?? null) === 'Nine specialist models, one simple upload'
        ? 'Nine models. One'
        : ($d['title'] ?? 'Nine models. One');
    $titleHighlight = $d['title_highlight'] ?? 'decision engine';
    $titleSuffix = $d['title_suffix'] ?? '';
    $subtitle = ($d['subtitle'] ?? null) === 'PixEnhance analyzes every image and routes it through the right enhancement path automatically.'
        ? 'PixEnhance routes every image through the model stack it actually needs — so you never have to know the difference between GAN, diffusion and transformer.'
        : ($d['subtitle'] ?? 'PixEnhance routes every image through the model stack it actually needs — so you never have to know the difference between GAN, diffusion and transformer.');

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

    $defaultTabs = [
        'upscale' => ['key' => 'upscale', 'icon' => 'maximize-2', 'title' => 'Super resolution', 'heading' => 'Enhance-XL v3 · Super resolution', 'badge' => 'Default', 'badge_variant' => 'primary', 'description' => 'A latent diffusion upscaler fine-tuned on 40M photographs. It hallucinates plausible micro-texture guided by the original edges, which is why fabric still looks like fabric at 16×.', 'tile_1_icon' => 'gauge', 'tile_1_name' => '2.4s', 'tile_1_meta' => '4× on A100', 'tile_2_icon' => 'maximize-2', 'tile_2_name' => '16,384px', 'tile_2_meta' => 'Max output edge', 'tile_3_icon' => 'activity', 'tile_3_name' => '0.94 SSIM', 'tile_3_meta' => 'Structural fidelity'],
        'faces' => ['key' => 'faces', 'icon' => 'scan-face', 'title' => 'Face restoration', 'heading' => 'FaceRestore v3 · Identity-safe', 'badge' => 'New', 'badge_variant' => 'success', 'description' => 'Detects every face in the frame, restores it independently at native crop resolution, then blends it back. An identity-similarity guard rejects any result that drifts from the original person.', 'tile_1_icon' => 'users', 'tile_1_name' => '64 faces', 'tile_1_meta' => 'Per image', 'tile_2_icon' => 'fingerprint', 'tile_2_name' => '0.91', 'tile_2_meta' => 'Identity retention', 'tile_3_icon' => 'scan-search', 'tile_3_name' => 'Auto', 'tile_3_meta' => 'Crop & blend'],
        'cleanup' => ['key' => 'cleanup', 'icon' => 'eraser', 'title' => 'Cleanup & denoise', 'heading' => 'CleanPass · Denoise, deblur, de-artefact', 'badge' => 'Pre-pass', 'badge_variant' => 'default', 'description' => 'Runs before upscaling so compression damage is never magnified. Handles sensor noise, JPEG blocking, chroma bleed and mild motion blur in a single forward pass.', 'tile_1_icon' => 'aperture', 'tile_1_name' => 'ISO 12800', 'tile_1_meta' => 'Noise ceiling', 'tile_2_icon' => 'eraser', 'tile_2_name' => 'q30+', 'tile_2_meta' => 'JPEG recovery', 'tile_3_icon' => 'timer', 'tile_3_name' => '0.4s', 'tile_3_meta' => 'Added latency'],
        'colour' => ['key' => 'colour', 'icon' => 'palette', 'title' => 'Colour & light', 'heading' => 'ToneLab · Colour, exposure & light', 'badge' => 'Optional', 'badge_variant' => 'default', 'description' => 'Recovers clipped highlights, lifts crushed shadows and neutralises colour casts using a scene-aware tone curve — no sliders, no presets, no muddy HDR look.', 'tile_1_icon' => 'palette', 'tile_1_name' => 'Auto WB', 'tile_1_meta' => 'Scene aware', 'tile_2_icon' => 'flame', 'tile_2_name' => '+2.1 EV', 'tile_2_meta' => 'Shadow recovery', 'tile_3_icon' => 'monitor', 'tile_3_name' => 'sRGB / P3', 'tile_3_meta' => 'Colour spaces'],
    ];
    $legacyIcons = [
        'ph ph-arrows-out' => 'maximize-2',
        'ph ph-user-focus' => 'scan-face',
        'ph ph-eraser' => 'eraser',
        'ph ph-palette' => 'palette',
    ];
    $items = is_array($d['items'] ?? null) && count($d['items']) > 0 ? $d['items'] : array_values($defaultTabs);
    $tabs = collect($items)
        ->filter(fn (array $item): bool => ! empty($item['title']))
        ->values()
        ->map(function (array $item, int $index) use ($defaultTabs, $legacyIcons): array {
            $key = \Illuminate\Support\Str::slug($item['key'] ?? $item['title'] ?? 'tab-'.$index);
            $defaults = $defaultTabs[$key] ?? [];
            $merged = array_replace($defaults, $item);
            $merged['key'] = $key;
            $merged['icon'] = $legacyIcons[$merged['icon'] ?? ''] ?? ($merged['icon'] ?? 'sparkles');
            $merged['badge_variant'] = $merged['badge_variant'] ?? ($index === 0 ? 'primary' : (($merged['badge'] ?? '') === 'New' ? 'success' : 'default'));
            $merged['tiles'] = collect([1, 2, 3])
                ->map(fn (int $tile): array => [
                    'icon' => $merged["tile_{$tile}_icon"] ?? '',
                    'name' => $merged["tile_{$tile}_name"] ?? '',
                    'meta' => $merged["tile_{$tile}_meta"] ?? '',
                ])
                ->filter(fn (array $tile): bool => $tile['name'] !== '' || $tile['meta'] !== '')
                ->values()
                ->all();

            return $merged;
        });
    $initialTab = \Illuminate\Support\Str::slug($tabs->first()['key'] ?? 'upscale');
@endphp

<section class="section section-surface" id="ai" aria-labelledby="ai-title">
    <div class="shell">
        <header class="section-head" data-reveal="up">
            <span class="badge badge-secondary">
                {!! $renderIcon($badgeIcon, 'brain') !!}
                {{ $badgeText }}
            </span>

            <h2 class="text-display-2" id="ai-title">
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

        <div class="tabs tabs-vertical mt-xl" x-data="tabs(@js($initialTab))" data-reveal="up">
            <!-- Tab list -->
            <div class="tabs__list" role="tablist" aria-label="AI capabilities" @keydown="onKeydown($event)">
                @foreach($tabs as $item)
                    @php($key = \Illuminate\Support\Str::slug($item['key'] ?? 'tab-'.$loop->index))
                    <button type="button" class="tabs__tab" role="tab"
                            :class="isActive(@js($key)) && 'is-active'"
                            :aria-selected="isActive(@js($key))"
                            :tabindex="isActive(@js($key)) ? 0 : -1"
                            id="tab-{{ $key }}" aria-controls="panel-{{ $key }}"
                            @click="select(@js($key))">
                        {!! $renderIcon($item['icon'] ?? null, 'sparkles') !!}
                        {{ $item['title'] }}
                    </button>
                @endforeach
            </div>

            <!-- Panels -->
            <div>
                @foreach($tabs as $item)
                    @php($key = \Illuminate\Support\Str::slug($item['key'] ?? 'tab-'.$loop->index))
                    <div class="tabs__panel" role="tabpanel" id="panel-{{ $key }}" aria-labelledby="tab-{{ $key }}"
                         x-show="isActive(@js($key))" x-cloak>
                        <div class="card card-pad-lg card-gradient-border-soft stack stack-md">
                            <div class="cluster cluster-between">
                                <h3 class="text-title">{{ $item['heading'] ?? $item['title'] }}</h3>
                                @if(! empty($item['badge']))
                                    <span class="badge @if(($item['badge_variant'] ?? '') === 'primary') badge-primary @elseif(($item['badge_variant'] ?? '') === 'success') badge-success @endif">{{ $item['badge'] }}</span>
                                @endif
                            </div>

                            <p class="text-body">{{ $item['description'] ?? '' }}</p>

                            @if(! empty($item['tiles']))
                                <div class="feature-grid feature-grid-3">
                                    @foreach($item['tiles'] as $tile)
                                        <div class="model-tile">
                                            <span class="model-tile__icon" aria-hidden="true">{!! $renderIcon($tile['icon'] ?? null, 'activity') !!}</span>
                                            <span>
                                                <span class="model-tile__name">{{ $tile['name'] }}</span>
                                                <span class="model-tile__meta">{{ $tile['meta'] }}</span>
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
