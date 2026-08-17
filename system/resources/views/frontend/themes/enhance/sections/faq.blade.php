@php
    $d = $section->data ?? [];

    $badgeText = $d['badge_text'] ?? 'Answers';
    $badgeIcon = $d['badge_icon'] ?? 'circle-help';
    $title = $d['title'] ?? 'Frequently asked questions';
    $subtitle = $d['subtitle'] ?? 'Everything about credits, quality, privacy and the API. Still stuck? Our team replies in under four hours.';
    $supportTitle = $d['support_title'] ?? 'Talk to a human';
    $supportBody = $d['support_body'] ?? 'Real support engineers, not a bot loop. Available Monday to Friday across every timezone.';
    $supportButtonText = $d['support_button_text'] ?? 'Contact support';
    $supportButtonLink = $d['support_button_link'] ?? '/support';
    $docsButtonText = $d['docs_button_text'] ?? 'Read the docs';
    $docsButtonLink = $d['docs_button_link'] ?? '/docs';

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

    $defaultItems = [
        ['question' => 'How is this different from Photoshop\'s "Super Resolution"?', 'answer' => 'Photoshop doubles resolution with a general-purpose model. PixEnhance runs a detection pass first, then routes your image through the specific models it needs — face restoration, denoise, edge recovery — at up to 16× instead of 2×.'],
        ['question' => 'What exactly is a credit?', 'answer' => 'One credit = one finished image, at any scale factor. Re-running the same source with different settings costs another credit, but downloading a result you already generated is always free. Credits on paid plans roll over and never expire while your subscription is active.'],
        ['question' => 'Who owns the enhanced images?', 'answer' => 'You do — on every plan, including the free tier. Paid plans add an explicit commercial licence. We never train on customer uploads, and source files are permanently deleted after 24 hours unless you keep them in your library.'],
        ['question' => 'Is there an API, and how hard is the integration?', 'answer' => 'One authenticated POST with your image URL or binary, and a webhook when the job finishes. Official SDKs cover PHP (Laravel-ready), Node and Python. Most teams ship their integration in an afternoon.'],
        ['question' => 'What happens if I hit my monthly credits?', 'answer' => 'Nothing breaks. You can buy a top-up pack at any time, or enable auto-recharge so pipelines never stall. There are no overage surprises — we only charge what you explicitly approve.'],
        ['question' => 'Can I cancel or switch plans later?', 'answer' => 'Any time, from your billing page. Upgrades apply instantly with a prorated charge; downgrades take effect at the end of the current period. The first 14 days are fully refundable, no questions asked.'],
    ];
    $legacyAnswers = [
        'Photoshop doubles resolution with a general-purpose model. PixEnhance runs a detection pass first, then routes your image through the specific models it needs - face restoration, denoise, edge recovery - at up to 16x instead of 2x.' => $defaultItems[0]['answer'],
        'One credit = one finished image, at any scale factor. Re-running the same source with different settings costs another credit, but downloading a result you already generated is always free.' => $defaultItems[1]['answer'],
        'You do - on every plan, including the free tier. Paid plans add an explicit commercial licence.' => $defaultItems[2]['answer'],
        'One authenticated POST with your image URL or binary, and a webhook when the job finishes. Official SDKs cover PHP, Node and Python.' => $defaultItems[3]['answer'],
        'Nothing breaks. You can buy a top-up pack at any time, or enable auto-recharge so pipelines never stall.' => $defaultItems[4]['answer'],
        'Any time, from your billing page. Upgrades apply instantly with a prorated charge; downgrades take effect at the end of the current period.' => $defaultItems[5]['answer'],
    ];
    $itemsSource = is_array($d['items'] ?? null) && count($d['items']) > 0 ? $d['items'] : $defaultItems;
    $items = collect($itemsSource)
        ->filter(fn (array $item): bool => ! empty($item['question']))
        ->values()
        ->map(fn (array $item): array => [
            'question' => $item['question'],
            'answer' => $legacyAnswers[$item['answer'] ?? ''] ?? ($item['answer'] ?? ''),
        ]);
@endphp

<section class="section" id="faq" aria-labelledby="faq-title">
    <div class="shell">
        <div class="showcase">
            <!-- Intro -->
            <div class="stack stack-md" data-reveal="right">
                <span class="badge badge-primary">
                    {!! $renderIcon($badgeIcon, 'circle-help') !!}
                    {{ $badgeText }}
                </span>

                <h2 class="text-display-2" id="faq-title">{{ $title }}</h2>

                <p class="text-lead">
                    {{ $subtitle }}
                </p>

                <div class="card card-tinted stack stack-sm">
                    <h3 class="text-title">{{ $supportTitle }}</h3>
                    <p class="text-small">
                        {{ $supportBody }}
                    </p>
                    <div class="cluster">
                        <a class="btn btn-primary btn-sm btn-arrow" href="{{ $supportButtonLink }}" data-ripple>
                            {{ $supportButtonText }}
                            <i data-lucide="arrow-right" class="icon-arrow"></i>
                        </a>
                        <a class="btn btn-ghost btn-sm" href="{{ $docsButtonLink }}">
                            {!! $renderIcon('book-open') !!}
                            {{ $docsButtonText }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Accordion -->
            <div class="accordion" x-data="accordion(0)" data-reveal="left">
                @foreach($items as $item)
                    <div class="accordion__item" :class="isOpen({{ $loop->index }}) && 'is-open'">
                        <h3>
                            <button type="button" class="accordion__trigger" @click="toggle({{ $loop->index }})"
                                    :aria-expanded="isOpen({{ $loop->index }})" aria-controls="faq-panel-{{ $loop->index }}" id="faq-trigger-{{ $loop->index }}">
                                {{ $item['question'] }}
                                <span class="accordion__icon" aria-hidden="true">{!! $renderIcon('chevron-down') !!}</span>
                            </button>
                        </h3>
                        <div class="accordion__panel" id="faq-panel-{{ $loop->index }}" role="region" aria-labelledby="faq-trigger-{{ $loop->index }}"
                             x-show="isOpen({{ $loop->index }})" x-collapse x-cloak>
                            <div class="accordion__body">
                                {{ $item['answer'] ?? '' }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
