<section class="pricing-hero" aria-labelledby="pricing-hero-title">
    <div class="decor" aria-hidden="true">
        <span class="glow-orb glow-orb-hero"></span>
    </div>

    <div class="shell pricing-hero__inner">
        <div class="pricing-hero__copy" data-reveal="up">
            <span class="badge badge-primary">
                <i data-lucide="gem"></i>
                {{ __('Simple credit pricing') }}
            </span>

            <h1 class="text-display-1 pricing-hero__title" id="pricing-hero-title">
                {{ __('Pricing that scales with your image pipeline.') }}
            </h1>

            <p class="text-lead pricing-hero__lead">
                {{ __('Start free, upgrade when volume grows, and keep every enhancement feature available on every paid plan.') }}
            </p>

            <div class="hero__actions">
                <a class="btn btn-primary btn-lg btn-arrow btn-glow" href="{{ route('register') }}" data-ripple>
                    {{ __('Start free') }}
                    <i data-lucide="arrow-right" class="icon-arrow"></i>
                </a>

                <a class="btn btn-secondary btn-lg" href="#pricing-compare">
                    <i data-lucide="table-2"></i>
                    {{ __('Compare plans') }}
                </a>
            </div>
        </div>

        <aside class="pricing-hero__panel card card-glass card-pad-lg" data-reveal="up" data-reveal-delay="1">
            <div class="pricing-hero__metric">
                <span>{{ __('Free start') }}</span>
                <strong>{{ __('10 credits') }}</strong>
            </div>
            <div class="pricing-hero__metric">
                <span>{{ __('Paid plans from') }}</span>
                <strong>$19</strong>
            </div>
            <div class="pricing-hero__metric">
                <span>{{ __('Maximum output') }}</span>
                <strong>16K</strong>
            </div>

            <ul class="pricing-hero__list">
                <li><i data-lucide="check"></i>{{ __('All AI models included') }}</li>
                <li><i data-lucide="check"></i>{{ __('Cancel or change plans anytime') }}</li>
                <li><i data-lucide="check"></i>{{ __('Commercial licence on paid plans') }}</li>
            </ul>
        </aside>
    </div>
</section>
