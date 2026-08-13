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
                        <th scope="col">{{ __('Starter') }}</th>
                        <th scope="col">{{ __('Pro') }}</th>
                        <th scope="col">{{ __('Scale') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">{{ __('Monthly credits') }}</th>
                        <td>300</td>
                        <td>1,500</td>
                        <td>10,000</td>
                    </tr>
                    <tr>
                        <th scope="row">{{ __('Maximum upscale') }}</th>
                        <td>4x</td>
                        <td>16x</td>
                        <td>16x</td>
                    </tr>
                    <tr>
                        <th scope="row">{{ __('Batch size') }}</th>
                        <td>10</td>
                        <td>200</td>
                        <td>{{ __('Custom') }}</td>
                    </tr>
                    <tr>
                        <th scope="row">{{ __('Face restoration and denoise') }}</th>
                        <td><i data-lucide="check" class="compare-check"></i></td>
                        <td><i data-lucide="check" class="compare-check"></i></td>
                        <td><i data-lucide="check" class="compare-check"></i></td>
                    </tr>
                    <tr>
                        <th scope="row">{{ __('Background removal') }}</th>
                        <td><i data-lucide="minus" class="compare-muted"></i></td>
                        <td><i data-lucide="check" class="compare-check"></i></td>
                        <td><i data-lucide="check" class="compare-check"></i></td>
                    </tr>
                    <tr>
                        <th scope="row">{{ __('API access and webhooks') }}</th>
                        <td><i data-lucide="minus" class="compare-muted"></i></td>
                        <td><i data-lucide="check" class="compare-check"></i></td>
                        <td><i data-lucide="check" class="compare-check"></i></td>
                    </tr>
                    <tr>
                        <th scope="row">{{ __('Priority GPU queue') }}</th>
                        <td><i data-lucide="minus" class="compare-muted"></i></td>
                        <td><i data-lucide="check" class="compare-check"></i></td>
                        <td><i data-lucide="check" class="compare-check"></i></td>
                    </tr>
                    <tr>
                        <th scope="row">{{ __('Dedicated capacity') }}</th>
                        <td><i data-lucide="minus" class="compare-muted"></i></td>
                        <td><i data-lucide="minus" class="compare-muted"></i></td>
                        <td><i data-lucide="check" class="compare-check"></i></td>
                    </tr>
                    <tr>
                        <th scope="row">{{ __('SSO and audit logs') }}</th>
                        <td><i data-lucide="minus" class="compare-muted"></i></td>
                        <td><i data-lucide="minus" class="compare-muted"></i></td>
                        <td><i data-lucide="check" class="compare-check"></i></td>
                    </tr>
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
