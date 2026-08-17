@php
    $d = $section->data ?? [];

    $badgeText = $d['badge_text'] ?? '';
    $badgeIcon = $d['badge_icon'] ?? 'star';
    $title = $d['title'] ?? 'Loved by people who zoom in';
    $subtitle = ($d['subtitle'] ?? null) === 'Photographers, e-commerce teams and archivists who care about the pixel level - and noticed the difference immediately.'
        ? 'Photographers, e-commerce teams and archivists who care about the pixel level — and noticed the difference immediately.'
        : ($d['subtitle'] ?? 'Photographers, e-commerce teams and archivists who care about the pixel level — and noticed the difference immediately.');
    $displayLimit = min(12, max(1, (int) ($d['display_limit'] ?? 6)));
    $verifiedLabel = $d['verified_label'] ?? 'Verified';

    $renderIcon = function (?string $icon, string $fallback = ''): string {
        $resolvedIcon = trim($icon ?: $fallback);

        if ($resolvedIcon === '') {
            return '';
        }

        if (str_starts_with($resolvedIcon, 'ph-')) {
            $resolvedIcon = 'ph '.$resolvedIcon;
        }

        if (str_starts_with($resolvedIcon, 'ph ')) {
            return '<i class="' . e($resolvedIcon) . '"></i>';
        }

        return '<i data-lucide="' . e($resolvedIcon) . '"></i>';
    };

    $avatarFallbacks = [
        asset('assets/frontend/enhance/img/avatars/avatar-1.svg'),
        asset('assets/frontend/enhance/img/avatars/avatar-3.svg'),
        asset('assets/frontend/enhance/img/avatars/avatar-4.svg'),
        asset('assets/frontend/enhance/img/avatars/avatar-2.svg'),
        asset('assets/frontend/enhance/img/avatars/avatar-5.svg'),
        asset('assets/frontend/enhance/img/avatars/avatar-6.svg'),
    ];

    $items = collect($testimonials ?? [])
        ->take($displayLimit)
        ->values()
        ->map(fn ($testimonial, int $index): array => [
            'client_name' => (string) data_get($testimonial, 'client_name', ''),
            'company_name' => (string) data_get($testimonial, 'company_name', ''),
            'designation' => (string) data_get($testimonial, 'designation', ''),
            'quote' => (string) data_get($testimonial, 'quote', ''),
            'rating' => min(5, max(1, (int) (data_get($testimonial, 'rating') ?: 5))),
            'avatar_url' => data_get($testimonial, 'avatar_url') ?: ($avatarFallbacks[$index % count($avatarFallbacks)] ?? $avatarFallbacks[0]),
        ])
        ->filter(fn (array $testimonial): bool => $testimonial['client_name'] !== '' && $testimonial['quote'] !== '')
        ->values();

    $averageRating = $items->isNotEmpty()
        ? number_format((float) $items->avg('rating'), 1)
        : '0.0';
@endphp

<section class="section section-surface" id="testimonials" aria-labelledby="testimonials-title">
    <div class="shell">
        <header class="section-head" data-reveal="up">
            <span class="badge badge-success">
                {!! $renderIcon($badgeIcon, 'star') !!}
                {{ $badgeText ?: $averageRating.' average from image teams' }}
            </span>

            <h2 class="text-display-2" id="testimonials-title">
                {{ $title }}
            </h2>

            <p class="text-lead">
                {{ $subtitle }}
            </p>
        </header>

        @if($items->isNotEmpty())
            <div class="testimonial-grid mt-xl" data-reveal="fade">
                @foreach($items as $testimonial)
                    @php
                        $rating = $testimonial['rating'];
                        $roleParts = collect([$testimonial['designation'], $testimonial['company_name']])->filter();
                    @endphp

                    <figure class="testimonial {{ $loop->first ? 'testimonial-featured' : '' }}">
                        @include('frontend.themes.enhance.components.rating', ['label' => __('Rated :rating out of 5', ['rating' => $rating])])

                        <blockquote class="testimonial__text">
                            <p>{{ $testimonial['quote'] }}</p>
                        </blockquote>

                        <figcaption class="testimonial__author">
                            <img class="testimonial__avatar" src="{{ $testimonial['avatar_url'] }}" alt="" width="40" height="40" loading="lazy">
                            <span>
                                <span class="testimonial__name">{{ $testimonial['client_name'] }}</span>
                                <span class="testimonial__role">{{ $roleParts->join(', ') }}</span>
                            </span>

                            @if($loop->first && $verifiedLabel)
                                <span class="badge badge-sm badge-success testimonial__badge">
                                    {!! $renderIcon('badge-check') !!}
                                    {{ $verifiedLabel }}
                                </span>
                            @endif
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        @else
            <div class="empty-state mt-xl" data-reveal="up">
                <span class="empty-state__icon" aria-hidden="true">{!! $renderIcon('message-square-quote') !!}</span>
                <h3 class="text-title">{{ __('Testimonials are being updated') }}</h3>
                <p class="text-body">{{ __('Add active testimonials in the Testimonials module to show customer proof here.') }}</p>
            </div>
        @endif
    </div>
</section>
