<section class="section" id="quality" aria-labelledby="quality-title">
    <div class="decor" aria-hidden="true">
        <span class="blob blob-md blob-secondary blob-section-left anim-drift"></span>
    </div>

    <div class="shell">
        <div class="showcase showcase-media-lead">
            <!-- Copy -->
            <div class="stack stack-md" data-reveal="right">
                <span class="badge badge-primary">
                    <i data-lucide="focus"></i>
                    Image quality comparison
                </span>

                <h2 class="text-display-2" id="quality-title">
                    Detail that was never
                    <span class="text-gradient anim-gradient">in the file</span>
                </h2>

                <p class="text-lead">
                    Interpolation just averages the pixels you already have. PixEnhance
                    predicts the detail your lens missed.
                </p>

                <ul class="showcase__list">
                    <li class="showcase__list-item">
                        <span class="showcase__list-icon" aria-hidden="true"><i data-lucide="check"></i></span>
                        <span>
                            <strong>No plastic skin, no halos</strong>
                            A perceptual loss function keeps texture believable instead of
                            over-smoothing everything into wax.
                        </span>
                    </li>
                    <li class="showcase__list-item">
                        <span class="showcase__list-icon" aria-hidden="true"><i data-lucide="check"></i></span>
                        <span>
                            <strong>JPEG artefacts removed first</strong>
                            Blocking and ringing are cleaned before upscaling, so compression
                            damage never gets amplified.
                        </span>
                    </li>
                    <li class="showcase__list-item">
                        <span class="showcase__list-icon" aria-hidden="true"><i data-lucide="check"></i></span>
                        <span>
                            <strong>Text and logos stay sharp</strong>
                            A dedicated edge pass keeps typography, packaging and UI
                            screenshots crisp and readable.
                        </span>
                    </li>
                </ul>

                <div class="cluster">
                    <a class="btn btn-primary btn-arrow" href="/upscaler" data-ripple>
                        Try it on your photo
                        <i data-lucide="arrow-right" class="icon-arrow"></i>
                    </a>
                    <a class="btn btn-outline" href="/docs#models">
                        <i data-lucide="cpu"></i>
                        Compare models
                    </a>
                </div>
            </div>

            <!-- Media -->
            <div class="compare compare-showcase" data-reveal="left" data-compare data-compare-start="45" data-compare-autoplay>
                <div class="compare__frame">
                    <img class="compare__layer" src="{{ asset('assets/frontend/enhance/img/samples/family-before.webp') }}"
                         alt="A 1920s family portrait as a low-resolution scan — soft, faded and marked by compression"
                         width="1300" height="1500" loading="lazy" decoding="async">
                    <img class="compare__layer compare__layer-after" src="{{ asset('assets/frontend/enhance/img/samples/family-after.webp') }}"
                         alt="The same portrait after AI restoration, with recovered facial detail, fabric texture and warmth"
                         width="1300" height="1500" loading="lazy" decoding="async">
                </div>

                <label class="sr-only" for="quality-compare">Reveal the enhanced photo</label>
                <input class="compare__range" type="range" id="quality-compare" data-compare-range
                       min="0" max="100" value="45" step="0.1" aria-label="Compare the original and enhanced photo">

                <span class="compare__tag compare__tag-before">Original</span>
                <span class="compare__tag compare__tag-after">
                    <i data-lucide="sparkles"></i>
                    PixEnhance
                </span>

                <span class="compare__meta compare__meta-before">JPEG · q35 · 512 px</span>
                <span class="compare__meta compare__meta-after">PNG · lossless · 4096 px</span>

                <span class="compare__handle" aria-hidden="true">
                    <span class="compare__grip"><i data-lucide="move-horizontal"></i></span>
                </span>
            </div>
        </div>
    </div>
</section>
