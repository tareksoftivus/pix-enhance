<section class="legal-hero" aria-labelledby="terms-hero-title">
    <div class="decor" aria-hidden="true">
        <span class="glow-orb glow-orb-hero"></span>
    </div>

    <div class="shell legal-hero__inner">
        <div class="legal-hero__copy" data-reveal="up">
            <span class="badge badge-primary">
                <i data-lucide="file-check-2"></i>
                {{ __('Legal') }}
            </span>

            <h1 class="text-display-1 legal-hero__title" id="terms-hero-title">
                {{ __('Terms & Conditions') }}
            </h1>

            <p class="text-lead legal-hero__lead">
                {{ __('The rules for using PixEnhance, managing your account and working with generated image outputs.') }}
            </p>
        </div>

        <aside class="legal-hero__panel card card-glass card-pad-lg" data-reveal="up" data-reveal-delay="1">
            <span>{{ __('Last updated') }}</span>
            <strong>{{ __('August 13, 2026') }}</strong>
            <p>{{ __('These terms apply to website, dashboard and API use.') }}</p>
        </aside>
    </div>
</section>
