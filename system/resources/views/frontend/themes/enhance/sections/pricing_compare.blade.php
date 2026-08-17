@php
    $plans = ($pricingPlans ?? collect())->values();

    if ($plans->isEmpty()) {
        $plans = collect([
            (object) [
                'name' => 'Starter',
                'credits_monthly' => 300,
                'features' => [
                    '300 image enhancement credits per month',
                    'Upscale up to 4x with 4K output',
                    'Face restoration and denoise',
                    'Batch processing for 10 images',
                ],
            ],
            (object) [
                'name' => 'Pro',
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
            ],
            (object) [
                'name' => 'Scale',
                'credits_monthly' => 10000,
                'features' => [
                    'Everything in Pro',
                    '10,000 image enhancement credits per month',
                    'Dedicated GPU capacity',
                    'Custom storage with S3, R2 or GCS',
                    'SSO, audit logs and team roles',
                ],
            ],
        ]);
    }

    $featureRows = $plans
        ->flatMap(fn ($plan) => $plan->features ?? [])
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
                <i data-lucide="table-2"></i>
                {{ __('Plan comparison') }}
            </span>

            <h2 class="text-display-2" id="pricing-compare-title">
                {{ __('Choose by volume, not by missing features.') }}
            </h2>

            <p class="text-lead">
                {{ __('Every paid plan includes the same enhancement engine. Higher tiers add credits, throughput and team controls.') }}
            </p>
        </header>

        <div class="compare-table-wrap card card-glass card-flush mt-xl" data-reveal="up" data-reveal-delay="1">
            <table class="compare-table">
                <caption class="sr-only">{{ __('Pricing plan feature comparison') }}</caption>
                <thead>
                    <tr>
                        <th scope="col">{{ __('Feature') }}</th>
                        @foreach($plans as $plan)
                            <th scope="col">{{ $plan->name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">{{ __('Monthly credits') }}</th>
                        @foreach($plans as $plan)
                            <td>{{ number_format((int) $plan->credits_monthly) }}</td>
                        @endforeach
                    </tr>
                    @foreach($featureRows as $feature)
                        <tr>
                            <th scope="row">{{ $feature }}</th>
                            @foreach($plans as $plan)
                                @if(in_array($feature, $plan->features ?? [], true))
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

        <div class="pricing-compare__cta card card-tinted mt-lg" data-reveal="up" data-reveal-delay="2">
            <div>
                <h3 class="text-title">{{ __('Need a custom credit pool?') }}</h3>
                <p class="text-body">{{ __('We can shape volume, storage and SLA terms around your production workflow.') }}</p>
            </div>
            <a class="btn btn-primary btn-arrow" href="/contact" data-ripple>
                {{ __('Talk to sales') }}
                <i data-lucide="arrow-right" class="icon-arrow"></i>
            </a>
        </div>
    </div>
</section>
