<section class="section features-overview" id="features-overview" aria-labelledby="features-overview-title">
    <div class="shell">
        <header class="section-head" data-reveal="up">
            <span class="badge badge-secondary">
                <i data-lucide="layers"></i>
                {{ __('Core capabilities') }}
            </span>

            <h2 class="text-display-2" id="features-overview-title">
                {{ __('One workspace for cleanup, scale and delivery.') }}
            </h2>

            <p class="text-lead">
                {{ __('Every tool is tuned for fast decisions: upload once, choose an output, and keep quality consistent across the whole batch.') }}
            </p>
        </header>

        <div class="features-page-grid mt-xl">
            <article class="card card-hover-glow feature-card features-page-card features-page-card--media" data-reveal="up">
                <div class="feature-card__media">
                    <img src="{{ asset('assets/frontend/enhance/img/samples/feature-face.webp') }}"
                         alt="{{ __('Restored portrait with clear facial detail') }}"
                         width="900" height="562" loading="lazy" decoding="async">
                    <span class="feature-card__chip">
                        <i data-lucide="scan-face"></i>
                        {{ __('Identity safe') }}
                    </span>
                </div>
                <div class="feature-card__body">
                    <div class="feature-card__head">
                        <span class="feature-card__icon" aria-hidden="true"><i data-lucide="scan-face"></i></span>
                        <h3 class="feature-card__title">{{ __('Face restoration') }}</h3>
                    </div>
                    <p class="feature-card__text">{{ __('Recover eyes, skin texture and old scans while keeping the subject recognisable.') }}</p>
                </div>
            </article>

            <article class="card card-hover-glow feature-card features-page-card features-page-card--media" data-reveal="up" data-reveal-delay="1">
                <div class="feature-card__media">
                    <img src="{{ asset('assets/frontend/enhance/img/samples/feature-cutout.webp') }}"
                         alt="{{ __('Clean subject cutout separated from background') }}"
                         width="900" height="562" loading="lazy" decoding="async">
                    <span class="feature-card__chip feature-card__chip-accent">
                        <i data-lucide="eraser"></i>
                        {{ __('Transparent PNG') }}
                    </span>
                </div>
                <div class="feature-card__body">
                    <div class="feature-card__head">
                        <span class="feature-card__icon feature-card__icon-accent" aria-hidden="true"><i data-lucide="eraser"></i></span>
                        <h3 class="feature-card__title">{{ __('Background removal') }}</h3>
                    </div>
                    <p class="feature-card__text">{{ __('Create clean cutouts for product, portrait and campaign imagery with soft-edge detail preserved.') }}</p>
                </div>
            </article>

            <article class="card card-tinted features-mini-card" data-reveal="up" data-reveal-delay="2">
                <i data-lucide="maximize-2"></i>
                <h3>{{ __('Super resolution') }}</h3>
                <p>{{ __('Upscale images up to 16K with reconstructed texture and cleaner edges.') }}</p>
            </article>

            <article class="card card-tinted features-mini-card" data-reveal="up" data-reveal-delay="3">
                <i data-lucide="aperture"></i>
                <h3>{{ __('Denoise and deblur') }}</h3>
                <p>{{ __('Reduce ISO noise, motion softness and compression artefacts before export.') }}</p>
            </article>

            <article class="card card-tinted features-mini-card" data-reveal="up" data-reveal-delay="4">
                <i data-lucide="palette"></i>
                <h3>{{ __('Colour and exposure') }}</h3>
                <p>{{ __('Recover light, balance colour casts and keep the final image natural.') }}</p>
            </article>

            <article class="card card-tinted features-mini-card" data-reveal="up" data-reveal-delay="5">
                <i data-lucide="workflow"></i>
                <h3>{{ __('Batch workflow') }}</h3>
                <p>{{ __('Process high-volume folders with presets, queues and consistent output rules.') }}</p>
            </article>
        </div>
    </div>
</section>
