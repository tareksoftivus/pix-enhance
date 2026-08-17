@php
    $d = $section->data ?? [];

    $label = $d['label'] ?? 'Powering image pipelines at 4,000+ companies';
    $fallbackLogos = [
        asset('assets/frontend/enhance/img/brands/northwind.svg'),
        asset('assets/frontend/enhance/img/brands/pixelforge.svg'),
        asset('assets/frontend/enhance/img/brands/lumen.svg'),
        asset('assets/frontend/enhance/img/brands/atlasco.svg'),
        asset('assets/frontend/enhance/img/brands/vertexlab.svg'),
        asset('assets/frontend/enhance/img/brands/orbitly.svg'),
    ];

    $brands = collect($d['brands'] ?? [])
        ->filter(fn (array $brand): bool => ! empty($brand['name']))
        ->values()
        ->map(fn (array $brand, int $index): array => [
            'name' => $brand['name'] ?? '',
            'image_url' => media_url($brand['image'] ?? null) ?: ($fallbackLogos[$index] ?? null),
        ])
        ->filter(fn (array $brand): bool => ! empty($brand['image_url']));
@endphp

<section class="section-sm section-surface" aria-label="Customers using PixEnhance">
    <div class="shell">
        <div class="logo-wall" data-reveal="fade">
            <p class="logo-wall__label">{{ $label }}</p>

            <div class="marquee logo-marquee" style="--marquee-duration: 32s;">
                {{-- Track is duplicated so the -100% keyframe loops seamlessly; the copy is decorative. --}}
                <ul class="marquee__track logo-strip">
                    @foreach ($brands as $brand)
                        <li class="logo-strip__item">
                            <span class="logo-strip__mark" role="img" aria-label="{{ $brand['name'] }}"
                                style="--logo-src: url('{{ $brand['image_url'] }}');"></span>
                        </li>
                    @endforeach
                </ul>
                <ul class="marquee__track logo-strip" aria-hidden="true">
                    @foreach ($brands as $brand)
                        <li class="logo-strip__item">
                            <span class="logo-strip__mark"
                                style="--logo-src: url('{{ $brand['image_url'] }}');"></span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>
