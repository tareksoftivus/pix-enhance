<section class="section section-surface" id="ai" aria-labelledby="ai-title">
    <div class="shell">
        <header class="section-head" data-reveal="up">
            <span class="badge badge-secondary">
                <i data-lucide="brain"></i>
                Inside the engine
            </span>

            <h2 class="text-display-2" id="ai-title">
                Nine models. One
                <span class="text-gradient anim-gradient">decision engine</span>
            </h2>

            <p class="text-lead">
                PixEnhance routes every image through the model stack it actually needs —
                so you never have to know the difference between GAN, diffusion and transformer.
            </p>
        </header>

        <div class="tabs tabs-vertical mt-xl" x-data="tabs('upscale')" data-reveal="up">
            <!-- Tab list -->
            <div class="tabs__list" role="tablist" aria-label="AI capabilities" @keydown="onKeydown($event)">
                <button type="button" class="tabs__tab" role="tab"
                        :class="isActive('upscale') && 'is-active'"
                        :aria-selected="isActive('upscale')"
                        :tabindex="isActive('upscale') ? 0 : -1"
                        id="tab-upscale" aria-controls="panel-upscale"
                        @click="select('upscale')">
                    <i data-lucide="maximize-2"></i>
                    Super resolution
                </button>

                <button type="button" class="tabs__tab" role="tab"
                        :class="isActive('faces') && 'is-active'"
                        :aria-selected="isActive('faces')"
                        :tabindex="isActive('faces') ? 0 : -1"
                        id="tab-faces" aria-controls="panel-faces"
                        @click="select('faces')">
                    <i data-lucide="scan-face"></i>
                    Face restoration
                </button>

                <button type="button" class="tabs__tab" role="tab"
                        :class="isActive('cleanup') && 'is-active'"
                        :aria-selected="isActive('cleanup')"
                        :tabindex="isActive('cleanup') ? 0 : -1"
                        id="tab-cleanup" aria-controls="panel-cleanup"
                        @click="select('cleanup')">
                    <i data-lucide="eraser"></i>
                    Cleanup &amp; denoise
                </button>

                <button type="button" class="tabs__tab" role="tab"
                        :class="isActive('colour') && 'is-active'"
                        :aria-selected="isActive('colour')"
                        :tabindex="isActive('colour') ? 0 : -1"
                        id="tab-colour" aria-controls="panel-colour"
                        @click="select('colour')">
                    <i data-lucide="palette"></i>
                    Colour &amp; light
                </button>
            </div>

            <!-- Panels -->
            <div>
                <div class="tabs__panel" role="tabpanel" id="panel-upscale" aria-labelledby="tab-upscale"
                     x-show="isActive('upscale')" x-cloak>
                    <div class="card card-pad-lg card-gradient-border-soft stack stack-md">
                        <div class="cluster cluster-between">
                            <h3 class="text-title">Enhance-XL v3 · Super resolution</h3>
                            <span class="badge badge-primary">Default</span>
                        </div>

                        <p class="text-body">
                            A latent diffusion upscaler fine-tuned on 40M photographs. It
                            hallucinates plausible micro-texture guided by the original edges,
                            which is why fabric still looks like fabric at 16×.
                        </p>

                        <div class="feature-grid feature-grid-3">
                            <div class="model-tile">
                                <span class="model-tile__icon" aria-hidden="true"><i data-lucide="gauge"></i></span>
                                <span>
                                    <span class="model-tile__name">2.4s</span>
                                    <span class="model-tile__meta">4× on A100</span>
                                </span>
                            </div>
                            <div class="model-tile">
                                <span class="model-tile__icon" aria-hidden="true"><i data-lucide="maximize-2"></i></span>
                                <span>
                                    <span class="model-tile__name">16,384px</span>
                                    <span class="model-tile__meta">Max output edge</span>
                                </span>
                            </div>
                            <div class="model-tile">
                                <span class="model-tile__icon" aria-hidden="true"><i data-lucide="activity"></i></span>
                                <span>
                                    <span class="model-tile__name">0.94 SSIM</span>
                                    <span class="model-tile__meta">Structural fidelity</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tabs__panel" role="tabpanel" id="panel-faces" aria-labelledby="tab-faces"
                     x-show="isActive('faces')" x-cloak>
                    <div class="card card-pad-lg card-gradient-border-soft stack stack-md">
                        <div class="cluster cluster-between">
                            <h3 class="text-title">FaceRestore v3 · Identity-safe</h3>
                            <span class="badge badge-success">New</span>
                        </div>

                        <p class="text-body">
                            Detects every face in the frame, restores it independently at native
                            crop resolution, then blends it back. An identity-similarity guard
                            rejects any result that drifts from the original person.
                        </p>

                        <div class="feature-grid feature-grid-3">
                            <div class="model-tile">
                                <span class="model-tile__icon" aria-hidden="true"><i data-lucide="users"></i></span>
                                <span>
                                    <span class="model-tile__name">64 faces</span>
                                    <span class="model-tile__meta">Per image</span>
                                </span>
                            </div>
                            <div class="model-tile">
                                <span class="model-tile__icon" aria-hidden="true"><i data-lucide="fingerprint"></i></span>
                                <span>
                                    <span class="model-tile__name">0.91</span>
                                    <span class="model-tile__meta">Identity retention</span>
                                </span>
                            </div>
                            <div class="model-tile">
                                <span class="model-tile__icon" aria-hidden="true"><i data-lucide="scan-search"></i></span>
                                <span>
                                    <span class="model-tile__name">Auto</span>
                                    <span class="model-tile__meta">Crop &amp; blend</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tabs__panel" role="tabpanel" id="panel-cleanup" aria-labelledby="tab-cleanup"
                     x-show="isActive('cleanup')" x-cloak>
                    <div class="card card-pad-lg card-gradient-border-soft stack stack-md">
                        <div class="cluster cluster-between">
                            <h3 class="text-title">CleanPass · Denoise, deblur, de-artefact</h3>
                            <span class="badge">Pre-pass</span>
                        </div>

                        <p class="text-body">
                            Runs before upscaling so compression damage is never magnified.
                            Handles sensor noise, JPEG blocking, chroma bleed and mild motion blur
                            in a single forward pass.
                        </p>

                        <div class="feature-grid feature-grid-3">
                            <div class="model-tile">
                                <span class="model-tile__icon" aria-hidden="true"><i data-lucide="aperture"></i></span>
                                <span>
                                    <span class="model-tile__name">ISO 12800</span>
                                    <span class="model-tile__meta">Noise ceiling</span>
                                </span>
                            </div>
                            <div class="model-tile">
                                <span class="model-tile__icon" aria-hidden="true"><i data-lucide="eraser"></i></span>
                                <span>
                                    <span class="model-tile__name">q30+</span>
                                    <span class="model-tile__meta">JPEG recovery</span>
                                </span>
                            </div>
                            <div class="model-tile">
                                <span class="model-tile__icon" aria-hidden="true"><i data-lucide="timer"></i></span>
                                <span>
                                    <span class="model-tile__name">0.4s</span>
                                    <span class="model-tile__meta">Added latency</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tabs__panel" role="tabpanel" id="panel-colour" aria-labelledby="tab-colour"
                     x-show="isActive('colour')" x-cloak>
                    <div class="card card-pad-lg card-gradient-border-soft stack stack-md">
                        <div class="cluster cluster-between">
                            <h3 class="text-title">ToneLab · Colour, exposure &amp; light</h3>
                            <span class="badge">Optional</span>
                        </div>

                        <p class="text-body">
                            Recovers clipped highlights, lifts crushed shadows and neutralises
                            colour casts using a scene-aware tone curve — no sliders, no presets,
                            no muddy HDR look.
                        </p>

                        <div class="feature-grid feature-grid-3">
                            <div class="model-tile">
                                <span class="model-tile__icon" aria-hidden="true"><i data-lucide="palette"></i></span>
                                <span>
                                    <span class="model-tile__name">Auto WB</span>
                                    <span class="model-tile__meta">Scene aware</span>
                                </span>
                            </div>
                            <div class="model-tile">
                                <span class="model-tile__icon" aria-hidden="true"><i data-lucide="flame"></i></span>
                                <span>
                                    <span class="model-tile__name">+2.1 EV</span>
                                    <span class="model-tile__meta">Shadow recovery</span>
                                </span>
                            </div>
                            <div class="model-tile">
                                <span class="model-tile__icon" aria-hidden="true"><i data-lucide="monitor"></i></span>
                                <span>
                                    <span class="model-tile__name">sRGB / P3</span>
                                    <span class="model-tile__meta">Colour spaces</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
