<section class="section section-surface features-ai" id="features-ai" aria-labelledby="features-ai-title">
    <div class="shell">
        <div class="features-ai__layout">
            <div class="stack stack-md" data-reveal="right">
                <span class="badge badge-primary">
                    <i data-lucide="brain"></i>
                    {{ __('AI routing') }}
                </span>

                <h2 class="text-display-2" id="features-ai-title">
                    {{ __('The right model runs before you have to choose it.') }}
                </h2>

                <p class="text-lead">
                    {{ __('PixEnhance detects faces, noise, compression, edges and colour problems, then builds the processing path for each image automatically.') }}
                </p>

                <div class="cluster">
                    <a class="btn btn-primary btn-arrow" href="/upscaler" data-ripple>
                        {{ __('Try the upscaler') }}
                        <i data-lucide="arrow-right" class="icon-arrow"></i>
                    </a>
                    <a class="btn btn-outline" href="/docs#models">
                        <i data-lucide="cpu"></i>
                        {{ __('View model docs') }}
                    </a>
                </div>
            </div>

            <div class="features-ai__stack" data-reveal="left">
                <article class="model-tile features-ai__tile">
                    <span class="model-tile__icon" aria-hidden="true"><i data-lucide="scan-search"></i></span>
                    <span>
                        <span class="model-tile__name">{{ __('Scene analysis') }}</span>
                        <span class="model-tile__meta">{{ __('Detects faces, objects, text, noise and blur.') }}</span>
                    </span>
                </article>
                <article class="model-tile features-ai__tile">
                    <span class="model-tile__icon" aria-hidden="true"><i data-lucide="route"></i></span>
                    <span>
                        <span class="model-tile__name">{{ __('Adaptive routing') }}</span>
                        <span class="model-tile__meta">{{ __('Sends every image through only the models it needs.') }}</span>
                    </span>
                </article>
                <article class="model-tile features-ai__tile">
                    <span class="model-tile__icon" aria-hidden="true"><i data-lucide="shield-check"></i></span>
                    <span>
                        <span class="model-tile__name">{{ __('Quality guards') }}</span>
                        <span class="model-tile__meta">{{ __('Checks identity, edge halos and export readiness.') }}</span>
                    </span>
                </article>
                <article class="model-tile features-ai__tile">
                    <span class="model-tile__icon" aria-hidden="true"><i data-lucide="download"></i></span>
                    <span>
                        <span class="model-tile__name">{{ __('Production exports') }}</span>
                        <span class="model-tile__meta">{{ __('Delivers PNG, WebP and large-format outputs for teams.') }}</span>
                    </span>
                </article>
            </div>
        </div>
    </div>
</section>
