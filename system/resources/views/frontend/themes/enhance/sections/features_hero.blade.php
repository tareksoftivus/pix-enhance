<section class="features-hero" aria-labelledby="features-hero-title">
    <div class="decor" aria-hidden="true">
        <span class="glow-orb glow-orb-hero"></span>
    </div>

    <div class="shell features-hero__inner">
        <div class="features-hero__copy" data-reveal="up">
            <span class="badge badge-primary">
                <i data-lucide="sparkles"></i>
                {{ __('Enhancement suite') }}
            </span>

            <h1 class="text-display-1 features-hero__title" id="features-hero-title">
                {{ __('Features built for every image workflow.') }}
            </h1>

            <p class="text-lead features-hero__lead">
                {{ __('Upscale, restore, clean, recolour and prepare production-ready images from one focused workspace.') }}
            </p>

            <div class="hero__actions">
                <a class="btn btn-primary btn-lg btn-arrow btn-glow" href="{{ route('register') }}" data-ripple>
                    {{ __('Start free') }}
                    <i data-lucide="arrow-right" class="icon-arrow"></i>
                </a>

                <a class="btn btn-secondary btn-lg" href="#features-overview">
                    <i data-lucide="layout-grid"></i>
                    {{ __('Explore features') }}
                </a>
            </div>
        </div>

        <div class="features-hero__visual" data-reveal="up" data-reveal-delay="1">
            <div class="compare compare-showcase" data-compare data-compare-start="52" data-compare-autoplay>
                <div class="compare__frame">
                    <img class="compare__layer" src="{{ asset('assets/frontend/enhance/img/samples/valley-before.webp') }}"
                         alt="{{ __('Soft mountain valley photo before AI enhancement') }}"
                         width="1200" height="750" loading="eager" decoding="async">
                    <img class="compare__layer compare__layer-after" src="{{ asset('assets/frontend/enhance/img/samples/valley-after.webp') }}"
                         alt="{{ __('Detailed mountain valley photo after AI enhancement') }}"
                         width="1200" height="750" loading="eager" decoding="async">
                </div>

                <label class="sr-only" for="features-hero-compare">{{ __('Reveal enhanced preview') }}</label>
                <input class="compare__range" type="range" id="features-hero-compare" data-compare-range
                       min="0" max="100" value="52" step="0.1" aria-label="{{ __('Compare original and enhanced preview') }}">

                <span class="compare__tag compare__tag-before">{{ __('Original') }}</span>
                <span class="compare__tag compare__tag-after">
                    <i data-lucide="sparkles"></i>
                    {{ __('Enhanced') }}
                </span>

                <span class="compare__handle" aria-hidden="true">
                    <span class="compare__grip"><i data-lucide="move-horizontal"></i></span>
                </span>
            </div>

            <div class="features-hero__metrics card card-glass">
                <div>
                    <strong>16K</strong>
                    <span>{{ __('max output') }}</span>
                </div>
                <div>
                    <strong>9</strong>
                    <span>{{ __('AI models') }}</span>
                </div>
                <div>
                    <strong>2.4s</strong>
                    <span>{{ __('avg process') }}</span>
                </div>
            </div>
        </div>
    </div>
</section>
