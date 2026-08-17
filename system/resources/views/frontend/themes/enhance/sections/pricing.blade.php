@php
    $plans = ($pricingPlans ?? collect())->values();

    if ($plans->isEmpty()) {
        $plans = collect([
            (object) [
                'name' => 'Starter',
                'slug' => 'starter',
                'tagline' => 'For side projects and occasional image rescue jobs.',
                'price_monthly' => 19,
                'price_yearly' => 180,
                'credits_monthly' => 300,
                'features' => [
                    '300 image enhancement credits per month',
                    'Upscale up to 4x with 4K output',
                    'Face restoration and denoise',
                    'Batch processing for 10 images',
                ],
                'cta_label' => 'Start free trial',
                'is_featured' => false,
            ],
            (object) [
                'name' => 'Pro',
                'slug' => 'pro',
                'tagline' => 'For photographers, agencies and busy storefronts.',
                'price_monthly' => 49,
                'price_yearly' => 468,
                'credits_monthly' => 1500,
                'features' => [
                    'Everything in Starter',
                    '1,500 image enhancement credits per month',
                    'Upscale up to 16x with 16K output',
                    'All nine AI models including background removal',
                    'Batch processing for 200 images',
                    'API access and webhooks',
                    'Commercial licence included',
                ],
                'cta_label' => 'Get started',
                'is_featured' => true,
            ],
            (object) [
                'name' => 'Scale',
                'slug' => 'scale',
                'tagline' => 'For marketplaces and product teams running pipelines.',
                'price_monthly' => 199,
                'price_yearly' => 1908,
                'credits_monthly' => 10000,
                'features' => [
                    'Everything in Pro',
                    '10,000 image enhancement credits per month',
                    'Dedicated GPU capacity',
                    'Custom storage with S3, R2 or GCS',
                    'SSO, audit logs and team roles',
                ],
                'cta_label' => 'Talk to sales',
                'is_featured' => false,
            ],
        ]);
    }

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
                    <i data-lucide="gem"></i>
                    {{ __('Simple credit pricing') }}
                </span>

                <h2 class="text-display-2" id="pricing-title">
                    {{ __('Pay for pixels, not') }}
                    <span class="text-gradient anim-gradient">{{ __('seats') }}</span>
                </h2>

                <p class="text-lead">
                    {{ __('One credit enhances one image at any scale. Credits never expire on paid plans, and every tier includes the full model catalogue.') }}
                </p>

                <div class="billing-toggle">
                    <span class="billing-toggle__label" :class="!yearly && 'is-active'">{{ __('Monthly') }}</span>

                    <button type="button" class="switch" role="switch"
                            :aria-checked="yearly" @click="toggle()"
                            aria-label="{{ __('Switch to yearly billing') }}">
                        <span class="switch__thumb"></span>
                    </button>

                    <span class="billing-toggle__label" :class="yearly && 'is-active'">{{ __('Yearly') }}</span>
                    <span class="billing-toggle__save">{{ __('Save more') }}</span>
                </div>
            </header>

            <div class="pricing-grid {{ $gridClass }} mt-xl">
                @foreach($plans as $plan)
                    @php
                        $monthlyPrice = (int) $plan->price_monthly;
                        $yearlyPrice = (int) $plan->price_yearly;
                        $yearlyMonthlyPrice = $yearlyPrice > 0 ? (int) round($yearlyPrice / 12) : $monthlyPrice;
                        $features = array_values($plan->features ?? []);
                        $ctaLabel = $plan->cta_label ?: __('Start free');
                        $ctaUrl = str_contains(strtolower($ctaLabel), 'sales') ? '/contact' : route('register');
                    @endphp

                    <article class="plan{{ $plan->is_featured ? ' plan-featured' : '' }}" data-reveal="up" data-reveal-delay="{{ min($loop->iteration, 3) }}">
                        @if($plan->is_featured)
                            <span class="plan__ribbon">{{ __('Most popular') }}</span>
                        @endif

                        <header class="plan__head">
                            <h3 class="plan__name">
                                {{ $plan->name }}
                                @if($plan->is_featured)
                                    <span class="badge badge-sm badge-primary">{{ __('16K') }}</span>
                                @endif
                            </h3>
                            <p class="plan__tagline">{{ $plan->tagline }}</p>
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

                        <a class="btn {{ $plan->is_featured ? 'btn-primary btn-arrow' : 'btn-outline' }} btn-block" href="{{ $ctaUrl }}" data-ripple>
                            {{ $ctaLabel }}
                            @if($plan->is_featured)
                                <i data-lucide="arrow-right" class="icon-arrow"></i>
                            @endif
                        </a>

                        <ul class="plan__features">
                            @foreach($features as $feature)
                                <li class="plan__feature">
                                    <span class="plan__feature-check" aria-hidden="true"><i data-lucide="check"></i></span>
                                    <span>{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </article>
                @endforeach
            </div>

            <p class="text-small cluster cluster-center mt-lg" data-reveal="fade">
                <i data-lucide="shield-check"></i>
                {{ __('14-day money-back guarantee · Cancel anytime ·') }}
                <a class="btn-link" href="{{ request()->is('pricing') ? '#pricing-compare' : '/pricing#pricing-compare' }}">{{ __('Compare all features') }}</a>
            </p>
        </div>
    </div>
</section>
