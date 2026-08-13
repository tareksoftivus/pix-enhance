<section class="section" id="features" aria-labelledby="features-title">
    <div class="decor" aria-hidden="true">
        <span class="blob blob-md blob-primary blob-section-right anim-drift"></span>
    </div>

    <div class="shell">
        <header class="section-head" data-reveal="up">
            <span class="badge badge-primary">
                <i data-lucide="layers"></i>
                Everything included
            </span>

            <h2 class="text-display-2" id="features-title">
                One platform for every
                <span class="text-gradient anim-gradient">image problem</span>
            </h2>

            <p class="text-lead">
                Nine specialised models behind a single, ridiculously simple interface.
                Upload once, and let PixEnhance decide exactly what your photo needs.
            </p>
        </header>

        <div class="bento-grid mt-xl" data-reveal="fade">
            <!-- 1 · Lead tile -->
            <article class="card card-hover-glow feature-card feature-card-lead bento-feature" data-reveal="up" data-reveal-delay="1">
                <div class="feature-card__media">
                    <div class="compare" data-compare data-compare-start="48" data-compare-autoplay>
                        <div class="compare__frame">
                            <img class="compare__layer" src="{{ asset('assets/frontend/enhance/img/samples/valley-before.webp') }}"
                                 alt="Mountain valley photograph at low resolution, soft and flat in colour" width="1200" height="750" loading="lazy" decoding="async">
                            <img class="compare__layer compare__layer-after" src="{{ asset('assets/frontend/enhance/img/samples/valley-after.webp') }}"
                                 alt="The same valley after 8× AI upscaling, with recovered detail and deeper colour" width="1200" height="750" loading="lazy" decoding="async">
                        </div>

                        <label class="sr-only" for="features-compare">Reveal enhanced landscape</label>
                        <input class="compare__range" type="range" id="features-compare" data-compare-range
                               min="0" max="100" value="48" step="0.1" aria-label="Compare original and upscaled landscape">

                        <span class="compare__tag compare__tag-before">1024px</span>
                        <span class="compare__tag compare__tag-after">
                            <i data-lucide="sparkles"></i>
                            8192px
                        </span>

                        <span class="compare__handle" aria-hidden="true">
                            <span class="compare__grip"><i data-lucide="move-horizontal"></i></span>
                        </span>
                    </div>
                </div>

                <div class="feature-card__body">
                    <div class="feature-card__head">
                        <span class="feature-card__icon" aria-hidden="true">
                            <i data-lucide="maximize-2"></i>
                        </span>
                        <h3 class="feature-card__title">True detail upscaling up to 16K</h3>
                    </div>

                    <p class="feature-card__text">
                        Most upscalers stretch pixels and call it a day. Our diffusion-guided model
                        reconstructs texture — fabric weave, skin pores, foliage, typography — using
                        what it learned from 40 million high-resolution photographs.
                    </p>
                </div>
            </article>

            <!-- 2 · Face restoration -->
            <article class="card card-hover-glow feature-card" data-reveal="up" data-reveal-delay="2">
                <div class="feature-card__media">
                    <img src="{{ asset('assets/frontend/enhance/img/samples/feature-face.webp') }}"
                         alt="Close portrait of a woman with sharply resolved eyes and skin texture"
                         width="900" height="562" loading="lazy" decoding="async">
                    <span class="feature-card__chip">
                        <i data-lucide="scan-face"></i>
                        Identity preserved
                    </span>
                </div>

                <div class="feature-card__body">
                    <div class="feature-card__head">
                        <span class="feature-card__icon feature-card__icon-secondary" aria-hidden="true">
                            <i data-lucide="scan-face"></i>
                        </span>
                        <h3 class="feature-card__title">Face restoration</h3>
                    </div>
                    <p class="feature-card__text">
                        Rebuilds eyes, teeth and skin texture in blurred, compressed or decades-old
                        scans — while keeping the person unmistakably themselves.
                    </p>
                </div>
            </article>

            <!-- 3 · Background removal -->
            <article class="card card-hover-glow feature-card" data-reveal="up" data-reveal-delay="3">
                <div class="feature-card__media">
                    <img src="{{ asset('assets/frontend/enhance/img/samples/feature-cutout.webp') }}"
                         alt="Photographer holding a camera, cleanly separated from a leafy background"
                         width="900" height="562" loading="lazy" decoding="async">
                    <span class="feature-card__chip feature-card__chip-accent">
                        <i data-lucide="eraser"></i>
                        Transparent PNG
                    </span>
                </div>

                <div class="feature-card__body">
                    <div class="feature-card__head">
                        <span class="feature-card__icon feature-card__icon-accent" aria-hidden="true">
                            <i data-lucide="eraser"></i>
                        </span>
                        <h3 class="feature-card__title">Background removal</h3>
                    </div>
                    <p class="feature-card__text">
                        Alpha-accurate cut-outs that survive hair, fur, glass and motion blur.
                        Export transparent PNG or drop in a new backdrop instantly.
                    </p>
                </div>
            </article>
        </div>
    </div>
</section>
