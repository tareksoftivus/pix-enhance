@php
    $d = $section->data ?? [];

    $badgeText = $d['badge_text'] ?? 'Plan comparison';
    $badgeIcon = $d['badge_icon'] ?? 'table-2';
    $title = $d['title'] ?? 'Choose by volume, not by';
    $titleHighlight = $d['title_highlight'] ?? null;
    $titleSuffix = $d['title_suffix'] ?? '.';

    if ($titleHighlight === null && $title === 'Choose by volume, not by missing features.') {
        $title = 'Choose by volume, not by';
        $titleHighlight = 'missing features';
    }

    $titleHighlight ??= 'missing features';
    $subtitle = $d['subtitle'] ?? 'Every paid plan includes the same enhancement engine. Higher tiers add credits, throughput and team controls.';
    $featureColumnLabel = $d['feature_column_label'] ?? 'Feature';
    $creditsRowLabel = $d['credits_row_label'] ?? 'Monthly credits';
    $emptyTitle = $d['empty_title'] ?? 'Pricing comparison is being updated';
    $emptyText = $d['empty_text'] ?? 'Add active plans in the Pricing Plan module to build the comparison table.';
    $ctaTitle = $d['cta_title'] ?? 'Need a custom credit pool?';
    $ctaBody = $d['cta_body'] ?? 'We can shape volume, storage and SLA terms around your production workflow.';
    $ctaButtonText = $d['cta_button_text'] ?? 'Talk to sales';
    $ctaButtonLink = $d['cta_button_link'] ?? '/contact';

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

    $plans = collect($pricingPlans ?? [])
        ->map(function ($plan): array {
            $features = collect($plan->features ?? [])
                ->filter(fn ($feature): bool => filled($feature))
                ->map(fn ($feature): string => (string) $feature)
                ->reject(fn (string $feature): bool => str_contains(strtolower($feature), 'credit'))
                ->values()
                ->all();

            return [
                'name' => (string) ($plan->name ?? ''),
                'credits_monthly' => (int) ($plan->credits_monthly ?? 0),
                'features' => $features,
            ];
        })
        ->filter(fn (array $plan): bool => $plan['name'] !== '')
        ->values();

    $featureRows = $plans
        ->flatMap(fn (array $plan): array => $plan['features'])
        ->unique()
        ->values();
@endphp

<section class="section pricing-compare" id="pricing-compare" aria-labelledby="pricing-compare-title">
    <div class="decor" aria-hidden="true">
        <span class="blob blob-md blob-secondary blob-section-left anim-drift"></span>
    </div>

    <div class="shell">
        <header class="section-head" data-reveal="up">
            <span class="badge badge-primary">
                {!! $renderIcon($badgeIcon, 'table-2') !!}
                {{ $badgeText }}
            </span>

            <h2 class="text-display-2" id="pricing-compare-title">
                {{ $title }}
                @if($titleHighlight)
                    <span class="text-gradient anim-gradient">{{ $titleHighlight }}</span>@endif{{ $titleSuffix }}
            </h2>

            <p class="text-lead">
                {{ $subtitle }}
            </p>
        </header>

        @if($plans->isNotEmpty())
            <div class="compare-table-wrap card card-glass card-flush mt-xl" data-reveal="up" data-reveal-delay="1">
                <table class="compare-table">
                    <caption class="sr-only">{{ __('Pricing plan feature comparison') }}</caption>
                    <thead>
                        <tr>
                            <th scope="col">{{ $featureColumnLabel }}</th>
                            @foreach($plans as $plan)
                                <th scope="col">{{ $plan['name'] }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">{{ $creditsRowLabel }}</th>
                            @foreach($plans as $plan)
                                <td>{{ number_format($plan['credits_monthly']) }}</td>
                            @endforeach
                        </tr>
                        @foreach($featureRows as $feature)
                            <tr>
                                <th scope="row">{{ $feature }}</th>
                                @foreach($plans as $plan)
                                    @if(in_array($feature, $plan['features'], true))
                                        <td><i data-lucide="check" class="compare-check"></i></td>
                                    @else
                                        <td><i data-lucide="minus" class="compare-muted"></i></td>
                                    @endif
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state mt-xl" data-reveal="up">
                <span class="empty-state__icon" aria-hidden="true">{!! $renderIcon('table-2') !!}</span>
                <h3 class="text-title">{{ $emptyTitle }}</h3>
                <p class="text-body">{{ $emptyText }}</p>
            </div>
        @endif

        <div class="pricing-compare__cta card card-tinted mt-lg" data-reveal="up" data-reveal-delay="2">
            <div>
                <h3 class="text-title">{{ $ctaTitle }}</h3>
                <p class="text-body">{{ $ctaBody }}</p>
            </div>
            <a class="btn btn-primary btn-arrow" href="{{ $ctaButtonLink }}" data-ripple>
                {{ $ctaButtonText }}
                <i data-lucide="arrow-right" class="icon-arrow"></i>
            </a>
        </div>
    </div>
</section>
