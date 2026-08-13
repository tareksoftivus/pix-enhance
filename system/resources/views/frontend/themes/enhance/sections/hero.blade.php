<section class="hero" aria-labelledby="hero-title">
    <!-- Background decoration layer -->
    <div class="decor" aria-hidden="true">
        <span class="glow-orb glow-orb-hero"></span>
    </div>

    <div class="shell">
        <div class="hero__inner">
            <!-- ============================ COPY ============================ -->
            <div class="hero__copy">
                <div class="hero__review" data-reveal="up">
                    <ul class="avatar-stack avatar-stack-sm">
                        <li><img class="avatar-stack__item"
                                src="{{ asset('assets/frontend/enhance/img/avatars/avatar-1.svg') }}" alt="" width="32"
                                height="32" loading="eager"></li>
                        <li><img class="avatar-stack__item"
                                src="{{ asset('assets/frontend/enhance/img/avatars/avatar-2.svg') }}" alt="" width="32"
                                height="32" loading="eager"></li>
                        <li><img class="avatar-stack__item"
                                src="{{ asset('assets/frontend/enhance/img/avatars/avatar-3.svg') }}" alt="" width="32"
                                height="32" loading="eager"></li>
                        <li><img class="avatar-stack__item"
                                src="{{ asset('assets/frontend/enhance/img/avatars/avatar-4.svg') }}" alt="" width="32"
                                height="32" loading="eager"></li>
                        <li><img class="avatar-stack__item"
                                src="{{ asset('assets/frontend/enhance/img/avatars/avatar-5.svg') }}" alt="" width="32"
                                height="32" loading="eager"></li>
                    </ul>

                    <span class="rating" aria-label="Rated 4.9 out of 5">
                        <i data-lucide="star"></i>
                        <i data-lucide="star"></i>
                        <i data-lucide="star"></i>
                        <i data-lucide="star"></i>
                        <i data-lucide="star"></i>
                    </span>

                    <p class="hero__review-text">
                        <strong>4.9/5</strong> from 2,480 creators
                    </p>
                </div>

                <h1 class="text-display-1 hero__title" id="hero-title" data-reveal="up" data-reveal-delay="1">
                    Upscale any image to
                    <span class="text-gradient anim-gradient">16K</span>
                    without losing a pixel
                </h1>

                <p class="text-lead hero__text" data-reveal="up" data-reveal-delay="2">
                    Rebuild real detail instead of stretching pixels. Upscale, restore and
                    clean up any photo in seconds.
                </p>

                <div class="hero__actions" data-reveal="up" data-reveal-delay="3">
                    <a class="btn btn-primary btn-lg btn-arrow btn-glow" href="{{ route('register') }}" data-ripple>
                        Enhance your first image
                        <i data-lucide="arrow-right" class="icon-arrow"></i>
                    </a>

                    <a class="btn btn-secondary btn-lg" href="#how-it-works">
                        <i data-lucide="circle-play"></i>
                        Watch 60s demo
                    </a>
                </div>

            </div>

            <!-- =========================== VISUAL =========================== -->
            <div class="hero__visual" data-reveal="zoom" data-reveal-delay="2">
                <div class="hero__stage">
                    <!-- Floating decorative cards -->
                    <div class="hero__float hero__float-a hero__float-hide-sm" aria-hidden="true">
                        <div class="ai-chip">
                            <span class="ai-chip__icon"><i data-lucide="scan-search"></i></span>
                            <span>
                                <span class="ai-chip__label">Detail recovered</span>
                                <span class="ai-chip__sub">Shoreline · foliage · texture</span>
                            </span>
                        </div>
                    </div>

                    <div class="hero__float hero__float-b hero__float-hide-sm" aria-hidden="true">
                        <div class="stat-chip">
                            <span class="stat-chip__icon stat-chip__icon-accent"><i
                                    data-lucide="trending-up"></i></span>
                            <span>
                                <span class="stat-chip__value">+412%</span>
                                <span class="stat-chip__label">Sharpness gain</span>
                            </span>
                        </div>
                    </div>

                    <div class="hero__float hero__float-d hero__float-hide-sm" aria-hidden="true">
                        <div class="stat-chip">
                            <span class="stat-chip__icon"><i data-lucide="crop"></i></span>
                            <span>
                                <span class="stat-chip__value">300 DPI</span>
                                <span class="stat-chip__label">Print ready</span>
                            </span>
                        </div>
                    </div>

                    <!--
                        App dashboard mock.
                        The comparison slider is genuinely interactive; the control rail
                        beside it is a static product illustration built from real form
                        controls, so it is marked inert and hidden from assistive tech.
                    -->
                    <div class="app-mock">
                        <div class="app-mock__bar">
                            <span class="app-mock__dots" aria-hidden="true">
                                <span></span><span></span><span></span>
                            </span>

                            <span class="app-mock__url">
                                <i data-lucide="lock"></i>
                                app.pixenhance.com/studio
                            </span>
                        </div>

                        <div class="app-mock__body">
                            <!-- Preview -->
                            <div class="app-mock__main">
                                <div class="compare compare-hero" data-compare data-compare-start="50"
                                    data-compare-autoplay>
                                    <div class="compare__frame">
                                        <img class="compare__layer"
                                            src="{{ asset('assets/frontend/enhance/img/samples/beach-before.webp') }}"
                                            alt="Low-resolution beach photograph before AI enhancement" width="1200"
                                            height="900" loading="eager" decoding="async" fetchpriority="high">
                                        <img class="compare__layer compare__layer-after"
                                            src="{{ asset('assets/frontend/enhance/img/samples/beach-after.webp') }}"
                                            alt="The same beach photograph after 4× AI upscaling, with sharper detail and richer colour"
                                            width="1200" height="900" loading="eager" decoding="async"
                                            fetchpriority="high">
                                    </div>

                                    <label class="sr-only" for="hero-compare">Reveal the enhanced image</label>
                                    <input class="compare__range" type="range" id="hero-compare" data-compare-range
                                        min="0" max="100" value="50" step="0.1"
                                        aria-label="Compare the original and enhanced image">

                                    <span class="compare__tag compare__tag-before">Before</span>
                                    <span class="compare__tag compare__tag-after">
                                        <i data-lucide="sparkles"></i>
                                        After
                                    </span>

                                    <span class="compare__meta compare__meta-before">960 × 720</span>
                                    <span class="compare__meta compare__meta-after">3840 × 2880</span>

                                    <span class="compare__hint">
                                        <i data-lucide="move-horizontal"></i>
                                        Drag to compare
                                    </span>

                                    <span class="compare__handle" aria-hidden="true">
                                        <span class="compare__grip">
                                            <i data-lucide="move-horizontal"></i>
                                        </span>
                                    </span>
                                </div>
                            </div>

                            <!-- Control rail — real inputs, presentation only -->
                            <div class="app-mock__side" inert aria-hidden="true">
                                <div class="app-mock__side-head">
                                    <span class="mock-panel__title">
                                        <i data-lucide="sliders-horizontal"></i>
                                        Enhance
                                    </span>
                                    <span class="badge badge-sm badge-primary">Auto</span>
                                </div>

                                <label class="file-field">
                                    <input class="file-field__input" type="file" name="source" accept="image/*"
                                        tabindex="-1">
                                    <span class="file-field__icon"><i data-lucide="image-plus"></i></span>
                                    <span class="file-field__body">
                                        <span class="file-field__name">beach-cove.jpg</span>
                                        <span class="file-field__meta">2.4 MB · 960 × 720</span>
                                    </span>
                                </label>

                                <div class="rail-row rail-row-stack">
                                    <label class="rail-row__label" for="mock-model">
                                        <i data-lucide="cpu"></i>
                                        Model
                                    </label>
                                    <select class="select rail-select" id="mock-model" name="model" tabindex="-1">
                                        <option selected>Enhance-XL v3</option>
                                        <option>Photo Real v2</option>
                                        <option>Illustration v1</option>
                                    </select>
                                </div>

                                <div class="rail-row">
                                    <span class="rail-row__label">
                                        <i data-lucide="maximize-2"></i>
                                        Scale
                                    </span>
                                    <span class="radio-group">
                                        <span class="radio-group__option">
                                            <input class="radio-group__input" type="radio" id="mock-scale-2"
                                                name="scale" value="2" tabindex="-1">
                                            <label class="radio-group__label" for="mock-scale-2">2×</label>
                                        </span>
                                        <span class="radio-group__option">
                                            <input class="radio-group__input" type="radio" id="mock-scale-4"
                                                name="scale" value="4" checked tabindex="-1">
                                            <label class="radio-group__label" for="mock-scale-4">4×</label>
                                        </span>
                                        <span class="radio-group__option">
                                            <input class="radio-group__input" type="radio" id="mock-scale-8"
                                                name="scale" value="8" tabindex="-1">
                                            <label class="radio-group__label" for="mock-scale-8">8×</label>
                                        </span>
                                    </span>
                                </div>

                                <div class="rail-row rail-row-stack">
                                    <span class="rail-row cluster-between">
                                        <label class="rail-row__label" for="mock-detail">
                                            <i data-lucide="focus"></i>
                                            Detail
                                        </label>
                                        <span class="rail-value">72%</span>
                                    </span>
                                    <input class="range rail-range" type="range" id="mock-detail" name="detail" min="0"
                                        max="100" value="72" tabindex="-1">
                                </div>

                                <div class="rail-row">
                                    <label class="rail-row__label" for="mock-face">
                                        <i data-lucide="scan-face"></i>
                                        Face restore
                                    </label>
                                    <span class="switch-field">
                                        <input class="switch-field__input" type="checkbox" id="mock-face" name="face"
                                            checked tabindex="-1">
                                        <span class="switch-field__track"></span>
                                    </span>
                                </div>

                                <div class="rail-row">
                                    <label class="rail-row__label" for="mock-denoise">
                                        <i data-lucide="eraser"></i>
                                        Denoise
                                    </label>
                                    <span class="switch-field">
                                        <input class="switch-field__input" type="checkbox" id="mock-denoise"
                                            name="denoise" checked tabindex="-1">
                                        <span class="switch-field__track"></span>
                                    </span>
                                </div>

                                <div class="rail-row">
                                    <label class="rail-row__label" for="mock-colour">
                                        <i data-lucide="palette"></i>
                                        Colour boost
                                    </label>
                                    <span class="switch-field">
                                        <input class="switch-field__input" type="checkbox" id="mock-colour"
                                            name="colour" checked tabindex="-1">
                                        <span class="switch-field__track"></span>
                                    </span>
                                </div>

                                <button class="btn btn-primary btn-sm btn-block rail-submit" type="button"
                                    tabindex="-1">
                                    <i data-lucide="sparkles"></i>
                                    Enhance image
                                </button>
                            </div>
                        </div>

                        <!-- Status bar -->
                        <div class="app-mock__foot">
                            <div class="foot-thumbs" aria-hidden="true">
                                <span class="foot-thumbs__label">Recent</span>
                                <span class="mock-thumb">
                                    <img src="{{ asset('assets/frontend/enhance/img/samples/thumb-1.webp') }}" alt=""
                                        width="320" height="320" loading="lazy">
                                </span>
                                <span class="mock-thumb">
                                    <img src="{{ asset('assets/frontend/enhance/img/samples/thumb-2.webp') }}" alt=""
                                        width="320" height="320" loading="lazy">
                                </span>
                                <span class="mock-thumb">
                                    <img src="{{ asset('assets/frontend/enhance/img/samples/thumb-3.webp') }}" alt=""
                                        width="320" height="320" loading="lazy">
                                </span>
                                <span class="mock-thumb mock-thumb-busy">
                                    <img src="{{ asset('assets/frontend/enhance/img/samples/thumb-4.webp') }}" alt=""
                                        width="320" height="320" loading="lazy">
                                    <span class="scanline"></span>
                                </span>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
