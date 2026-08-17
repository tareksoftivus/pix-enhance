@php
    $d = $section->data ?? [];

    $eyebrow = $d['eyebrow'] ?? __('The platform');
    $title = $d['title'] ?? __('Everything you need to make recognition stick');
    $subtitle =
        $d['subtitle'] ?? __('Six building blocks that turn everyday appreciation into a habit your whole team looks forward to.');

    $defaultFeatureImages = [
        0 => asset('assets/images/feature-1.png'),
        1 => asset('assets/images/feature-2.png'),
        2 => asset('assets/images/feature-3.png'),
        3 => asset('assets/images/feature-4.png'),
        4 => asset('assets/images/feature-5.png'),
        5 => asset('assets/images/feature-6.png'),
    ];

    $tintStyles = [
        'primary' => [
            'text' => '',
            'shadow' => 'box-shadow: 0 24px 60px -24px rgba(124,58,237,0.45)',
        ],
        'accent' => [
            'text' => 'text-accent',
            'shadow' => 'box-shadow: 0 24px 60px -24px rgba(245,158,11,0.45)',
        ],
        'info' => [
            'text' => 'text-info',
            'shadow' => 'box-shadow: 0 24px 60px -24px rgba(14,165,233,0.4)',
        ],
        'success' => [
            'text' => 'text-success',
            'shadow' => 'box-shadow: 0 24px 60px -24px rgba(22,163,74,0.4)',
        ],
        'deep' => [
            'text' => 'text-deep',
            'shadow' => 'box-shadow: 0 24px 60px -24px rgba(76,29,149,0.4)',
        ],
    ];

    $items = $d['items'] ?? [];

    $features = collect($items)
        ->values()
        ->map(function ($item, $index) use ($defaultFeatureImages, $tintStyles) {
            $tint = $item['tint'] ?? 'primary';
            $resolvedTint = $tintStyles[$tint] ?? $tintStyles['primary'];

            return [
                'title' => $item['title'] ?? '',
                'heading' => $item['heading'] ?? '',
                'description' => $item['description'] ?? '',
                'icon' => $item['icon'] ?? '',
                'link_text' => $item['link_text'] ?? __('Learn more'),
                'link_url' => $item['link_url'] ?? '#',
                'text_class' => $resolvedTint['text'],
                'stack_tint' => 'var(--color-' . $tint . ')',
                'image_url' => media_url($item['image'] ?? null) ?: ($defaultFeatureImages[$index] ?? null),
                'image_shadow' => $resolvedTint['shadow'],
            ];
        });
@endphp

<section class="pb-16 sm:pb-20 lg:pb-28">
    <div class="container">
        <div class="mx-auto max-w-2xl text-center" data-anim="reveal">
            @if ($eyebrow)
                <span class="eyebrow justify-center">
                    <i class="ph-fill ph-squares-four text-sm"></i>
                    {{ $eyebrow }}
                </span>
            @endif

            <h2 class="heading-2 mt-4">{{ $title }}</h2>

            @if ($subtitle)
                <p class="lead-text mt-4">
                    {{ $subtitle }}
                </p>
            @endif
        </div>

        @if ($features->isNotEmpty())
            <div class="mt-12 flex flex-col gap-6" data-stack>
                @foreach ($features as $feature)
                    <article class="stack-card" data-stack-tint="{{ $feature['stack_tint'] }}">
                        <div class="stack-card__body">
                            @if ($feature['title'])
                                <span class="eyebrow {{ $feature['text_class'] }}">
                                    @if ($feature['icon'])
                                        <i class="{{ $feature['icon'] }} text-sm"></i>
                                    @endif
                                    {{ $feature['title'] }}
                                </span>
                            @endif

                            @if ($feature['heading'])
                                <h3 class="heading-3 mt-3">{{ $feature['heading'] }}</h3>
                            @endif

                            @if ($feature['description'])
                                <p class="m-text mt-3 max-w-md">
                                    {{ $feature['description'] }}
                                </p>
                            @endif

                            @if ($feature['link_text'])
                                <a href="{{ $feature['link_url'] }}"
                                    class="mt-6 inline-flex items-center gap-2 font-semibold text-primary transition-colors hover:text-primary-dark">
                                    {{ $feature['link_text'] }} <i class="ph ph-arrow-right"></i>
                                </a>
                            @endif
                        </div>

                        <div class="stack-card__visual">
                            @if ($feature['image_url'])
                                <img src="{{ $feature['image_url'] }}" alt="{{ $feature['heading'] }}"
                                    class="w-full max-w-sm rounded-2xl" data-image-shadow="{{ $feature['image_shadow'] }}">
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
