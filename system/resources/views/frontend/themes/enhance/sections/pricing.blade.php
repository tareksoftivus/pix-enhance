@php
    $d = $section->data ?? [];

    $badgeText = $d['badge_text'] ?? 'Simple credit pricing';
    $badgeIcon = $d['badge_icon'] ?? 'gem';
    $title = ($d['title'] ?? null) === 'Pay for pixels, not seats'
        ? 'Pay for pixels, not'
        : ($d['title'] ?? 'Pay for pixels, not');
    $titleHighlight = $d['title_highlight'] ?? 'seats';
    $titleSuffix = $d['title_suffix'] ?? '';
    $subtitle = $d['subtitle'] ?? 'One credit enhances one image at any scale. Credits never expire on paid plans, and every tier includes the full model catalogue.';
    $billingMonthlyLabel = $d['billing_monthly_label'] ?? 'Monthly';
    $billingYearlyLabel = $d['billing_yearly_label'] ?? 'Yearly';
    $billingSaveLabel = ($d['billing_save_label'] ?? null) === 'Save more' ? 'Save 20%' : ($d['billing_save_label'] ?? 'Save 20%');
    $featuredBadgeText = $d['featured_badge_text'] ?? '16K';
    $featuredRibbonText = $d['featured_ribbon_text'] ?? 'Most popular';
    $emptyTitle = $d['empty_title'] ?? 'Pricing plans are being updated';
    $emptyText = $d['empty_text'] ?? 'Add active plans in the Pricing Plan module to show them here.';
    $note = $d['note'] ?? '14-day money-back guarantee · Cancel anytime ·';
    $compareLinkText = $d['compare_link_text'] ?? 'Compare all features';

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

    $modulePlans = collect($pricingPlans ?? [])->values();
    $plans = $modulePlans
        ->map(function ($plan): array {
            $slug = (string) ($plan->slug ?? \Illuminate\Support\Str::slug($plan->name ?? 'plan'));
            $features = array_values(array_filter($plan->features ?? [], fn ($feature): bool => filled($feature)));
            $creditsMonthly = (int) ($plan->credits_monthly ?? 0);

            if ($creditsMonthly > 0 && ! collect($features)->contains(fn (string $feature): bool => str_contains(strtolower($feature), 'credit'))) {
                array_unshift($features, number_format($creditsMonthly).' credits per month');
            }

            $ctaLabel = (string) (($plan->cta_label ?? '') ?: __('Start free'));
            $isSalesCta = str_contains(strtolower($ctaLabel), 'sales') || str_contains(strtolower($ctaLabel), 'contact');
            $ctaUrl = $isSalesCta
                ? url('/contact?plan='.$slug)
                : (\Illuminate\Support\Facades\Route::has('register') ? route('register', ['plan' => $slug]) : url('/register?plan='.$slug));

            return [
                'name' => (string) ($plan->name ?? ''),
                'slug' => $slug,
                'tagline' => (string) ($plan->tagline ?? ''),
                'icon' => $plan->icon ?? null,
                'price_monthly' => (int) ($plan->price_monthly ?? 0),
                'price_yearly' => (int) ($plan->price_yearly ?? 0),
                'credits_monthly' => $creditsMonthly,
                'features' => $features,
                'cta_label' => $ctaLabel,
                'cta_url' => $ctaUrl,
                'is_featured' => (bool) ($plan->is_featured ?? false),
            ];
        })
        ->filter(fn (array $plan): bool => $plan['name'] !== '')
        ->values();

    $gridClass = match (min($plans->count(), 4)) {
        1 => 'pricing-grid-1',
        2 => 'pricing-grid-2',
        default => 'pricing-grid-3',
    };
@endphp

<section class="section" id="pricing" aria-labelledby="pricing-title">
    <div class="decor" aria-hidden="true">
        <span class="blob blob-lg blob-primary blob-section-center anim-drift"></span>
    </div>

    <div class="shell">
        <div x-data="pricingToggle('monthly')">
            <header class="section-head" data-reveal="up">
                <span class="badge badge-primary">
                    {!! $renderIcon($badgeIcon, 'gem') !!}
                    {{ $badgeText }}
                </span>

                <h2 class="text-display-2" id="pricing-title">
                    {{ $title }}
                    @if($titleHighlight)
                        <span class="text-gradient anim-gradient">{{ $titleHighlight }}</span>
                    @endif
                    {{ $titleSuffix }}
                </h2>

                <p class="text-lead">
                    {{ $subtitle }}
                </p>

                <div class="billing-toggle">
                    <span class="billing-toggle__label" :class="!yearly && 'is-active'">{{ $billingMonthlyLabel }}</span>

                    <button type="button" class="switch" role="switch"
                            :aria-checked="yearly" @click="toggle()"
                            aria-label="{{ __('Switch to yearly billing') }}">
                        <span class="switch__thumb"></span>
                    </button>

                    <span class="billing-toggle__label" :class="yearly && 'is-active'">{{ $billingYearlyLabel }}</span>
                    <span class="billing-toggle__save">{{ $billingSaveLabel }}</span>
                </div>
            </header>

            @if($plans->isNotEmpty())
                <div class="pricing-grid {{ $gridClass }} mt-xl">
                    @foreach($plans as $plan)
                        @php
                            $monthlyPrice = $plan['price_monthly'];
                            $yearlyPrice = $plan['price_yearly'];
                            $yearlyMonthlyPrice = $yearlyPrice > 0 ? (int) round($yearlyPrice / 12) : $monthlyPrice;
                            $features = $plan['features'];
                            $ctaLabel = $plan['cta_label'];
                            $ctaUrl = $plan['cta_url'];
                        @endphp

                        <article class="plan{{ $plan['is_featured'] ? ' plan-featured' : '' }}" data-reveal="up" data-reveal-delay="{{ min($loop->iteration, 3) }}">
                            @if($plan['is_featured'] && $featuredRibbonText)
                                <span class="plan__ribbon">{{ $featuredRibbonText }}</span>
                            @endif

                            <header class="plan__head">
                                <h3 class="plan__name">
                                    @if(! empty($plan['icon']) && ! $plan['is_featured'])
                                        {!! $renderIcon($plan['icon']) !!}
                                    @endif
                                    {{ $plan['name'] }}
                                    @if($plan['is_featured'] && $featuredBadgeText)
                                        <span class="badge badge-sm badge-primary">{{ $featuredBadgeText }}</span>
                                    @endif
                                </h3>
                                @if($plan['tagline'] !== '')
                                    <p class="plan__tagline">{{ $plan['tagline'] }}</p>
                                @endif
                            </header>

                            <div>
                                <p class="plan__price">
                                    <span class="plan__currency">$</span>
                                    <span class="plan__amount" x-text="yearly ? @js(number_format($yearlyMonthlyPrice)) : @js(number_format($monthlyPrice))">{{ number_format($monthlyPrice) }}</span>
                                    <span class="plan__period">{{ __('/ month') }}</span>
                                </p>
                                <p class="plan__price-note">
                                    <span x-text="yearly ? @js(__('Billed :amount yearly', ['amount' => '$'.number_format($yearlyPrice)])) : @js(__('Billed monthly'))">{{ __('Billed monthly') }}</span>
                                    @if($yearlyPrice > 0 && $monthlyPrice > 0)
                                        <span class="plan__price-strike" x-show="yearly" x-cloak>${{ number_format($monthlyPrice * 12) }}</span>
                                    @endif
                                </p>
                            </div>

                            <a class="btn {{ $plan['is_featured'] ? 'btn-primary btn-arrow' : 'btn-outline' }} btn-block" href="{{ $ctaUrl }}" data-ripple>
                                {{ $ctaLabel }}
                                @if($plan['is_featured'])
                                    <i data-lucide="arrow-right" class="icon-arrow"></i>
                                @endif
                            </a>

                            @if(count($features) > 0)
                                <ul class="plan__features">
                                    @foreach($features as $feature)
                                        <li class="plan__feature">
                                            <span class="plan__feature-check" aria-hidden="true"><i data-lucide="check"></i></span>
                                            <span>{{ $feature }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </article>
                    @endforeach
                </div>
            @else
                <div class="empty-state mt-xl" data-reveal="up">
                    <span class="empty-state__icon" aria-hidden="true">{!! $renderIcon('gem') !!}</span>
                    <h3 class="text-title">{{ $emptyTitle }}</h3>
                    <p class="text-body">{{ $emptyText }}</p>
                </div>
            @endif

            <p class="text-small cluster cluster-center mt-lg" data-reveal="fade">
                <i data-lucide="shield-check"></i>
                {{ $note }}
                <a class="btn-link" href="{{ request()->is('pricing') ? '#pricing-compare' : '/pricing#pricing-compare' }}">{{ $compareLinkText }}</a>
            </p>
        </div>
    </div>
</section>
