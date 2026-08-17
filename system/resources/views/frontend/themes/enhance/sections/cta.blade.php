@php
    $d = $section->data ?? [];

    $badgeText = $d['badge_text'] ?? '10 free credits · no card required';
    $badgeIcon = $d['badge_icon'] ?? 'sparkles';
    $title = $d['title'] ?? 'Your next image deserves';
    $highlight = $d['highlight'] ?? 'every pixel';
    $titlePrefix = $highlight && str_ends_with($title, $highlight) ? trim(substr($title, 0, -strlen($highlight))) : $title;
    $body = ($d['body'] ?? null) === 'Upload a photo you had already given up on. If PixEnhance does not beat what you have now, you have lost ninety seconds.'
        ? 'Upload a photo you\'d already given up on. If PixEnhance doesn\'t beat what you have now, you\'ve lost ninety seconds.'
        : ($d['body'] ?? 'Upload a photo you\'d already given up on. If PixEnhance doesn\'t beat what you have now, you\'ve lost ninety seconds.');
    $buttonText = $d['button_text'] ?? 'Start enhancing free';
    $buttonLink = $d['button_link'] ?? '/register';
    $secondaryButtonText = $d['secondary_button_text'] ?? 'Talk to sales';
    $secondaryButtonLink = $d['secondary_button_link'] ?? '/contact';
    $defaultNotes = [
        ['text' => 'Set up in 30 seconds'],
        ['text' => 'Cancel anytime'],
        ['text' => 'Commercial licence included'],
    ];
    $notesSource = is_array($d['notes'] ?? null) && count($d['notes']) > 0 ? $d['notes'] : $defaultNotes;
    $notes = collect($notesSource)->filter(fn (array $note): bool => ! empty($note['text']))->values();

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

<section class="section" aria-labelledby="cta-title">
    <div class="shell">
        <div class="cta-panel" data-reveal="zoom">
            <div class="cta-panel__inner">
                <span class="badge badge-glass">
                    {!! $renderIcon($badgeIcon, 'sparkles') !!}
                    {{ $badgeText }}
                </span>

                <h2 class="text-display-2" id="cta-title">
                    {{ $titlePrefix }}
                    @if($highlight)
                        <span class="text-gradient anim-gradient">{{ $highlight }}</span>
                    @endif
                </h2>

                <p class="text-lead">
                    {{ $body }}
                </p>

                <div class="cta-panel__actions">
                    <a class="btn btn-primary btn-xl btn-arrow btn-glow" href="{{ url($buttonLink) }}" data-ripple>
                        {{ $buttonText }}
                        <i data-lucide="arrow-right" class="icon-arrow"></i>
                    </a>

                    <a class="btn btn-secondary btn-xl" href="{{ $secondaryButtonLink }}">
                        {!! $renderIcon('users') !!}
                        {{ $secondaryButtonText }}
                    </a>
                </div>

                <ul class="cta-panel__note trust-row">
                    @foreach($notes as $note)
                        <li class="trust-item">
                            {!! $renderIcon('check') !!}
                            <span>{{ $note['text'] ?? '' }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>
