<section class="section" id="faq" aria-labelledby="faq-title">
    <div class="shell">
        <div class="showcase">
            <!-- Intro -->
            <div class="stack stack-md" data-reveal="right">
                <span class="badge badge-primary">
                    <i data-lucide="circle-help"></i>
                    Answers
                </span>

                <h2 class="text-display-2" id="faq-title">Frequently asked questions</h2>

                <p class="text-lead">
                    Everything about credits, quality, privacy and the API. Still stuck?
                    Our team replies in under four hours.
                </p>

                <div class="card card-tinted stack stack-sm">
                    <h3 class="text-title">Talk to a human</h3>
                    <p class="text-small">
                        Real support engineers, not a bot loop. Available Monday to Friday
                        across every timezone.
                    </p>
                    <div class="cluster">
                        <a class="btn btn-primary btn-sm btn-arrow" href="/support" data-ripple>
                            Contact support
                            <i data-lucide="arrow-right" class="icon-arrow"></i>
                        </a>
                        <a class="btn btn-ghost btn-sm" href="/docs">
                            <i data-lucide="book-open"></i>
                            Read the docs
                        </a>
                    </div>
                </div>
            </div>

            <!-- Accordion -->
            <div class="accordion" x-data="accordion(0)" data-reveal="left">
                <div class="accordion__item" :class="isOpen(0) && 'is-open'">
                    <h3>
                        <button type="button" class="accordion__trigger" @click="toggle(0)"
                                :aria-expanded="isOpen(0)" aria-controls="faq-panel-0" id="faq-trigger-0">
                            How is this different from Photoshop's "Super Resolution"?
                            <span class="accordion__icon" aria-hidden="true"><i data-lucide="chevron-down"></i></span>
                        </button>
                    </h3>
                    <div class="accordion__panel" id="faq-panel-0" role="region" aria-labelledby="faq-trigger-0"
                         x-show="isOpen(0)" x-collapse x-cloak>
                        <div class="accordion__body">
                            Photoshop doubles resolution with a general-purpose model. PixEnhance
                            runs a detection pass first, then routes your image through the
                            specific models it needs — face restoration, denoise, edge recovery —
                            at up to 16× instead of 2×.
                        </div>
                    </div>
                </div>

                <div class="accordion__item" :class="isOpen(1) && 'is-open'">
                    <h3>
                        <button type="button" class="accordion__trigger" @click="toggle(1)"
                                :aria-expanded="isOpen(1)" aria-controls="faq-panel-1" id="faq-trigger-1">
                            What exactly is a credit?
                            <span class="accordion__icon" aria-hidden="true"><i data-lucide="chevron-down"></i></span>
                        </button>
                    </h3>
                    <div class="accordion__panel" id="faq-panel-1" role="region" aria-labelledby="faq-trigger-1"
                         x-show="isOpen(1)" x-collapse x-cloak>
                        <div class="accordion__body">
                            One credit = one finished image, at any scale factor. Re-running the
                            same source with different settings costs another credit, but
                            downloading a result you already generated is always free. Credits on
                            paid plans roll over and never expire while your subscription is active.
                        </div>
                    </div>
                </div>

                <div class="accordion__item" :class="isOpen(2) && 'is-open'">
                    <h3>
                        <button type="button" class="accordion__trigger" @click="toggle(2)"
                                :aria-expanded="isOpen(2)" aria-controls="faq-panel-2" id="faq-trigger-2">
                            Who owns the enhanced images?
                            <span class="accordion__icon" aria-hidden="true"><i data-lucide="chevron-down"></i></span>
                        </button>
                    </h3>
                    <div class="accordion__panel" id="faq-panel-2" role="region" aria-labelledby="faq-trigger-2"
                         x-show="isOpen(2)" x-collapse x-cloak>
                        <div class="accordion__body">
                            You do — on every plan, including the free tier. Paid plans add an
                            explicit commercial licence. We never train on customer uploads, and
                            source files are permanently deleted after 24 hours unless you keep
                            them in your library.
                        </div>
                    </div>
                </div>

                <div class="accordion__item" :class="isOpen(3) && 'is-open'">
                    <h3>
                        <button type="button" class="accordion__trigger" @click="toggle(3)"
                                :aria-expanded="isOpen(3)" aria-controls="faq-panel-3" id="faq-trigger-3">
                            Is there an API, and how hard is the integration?
                            <span class="accordion__icon" aria-hidden="true"><i data-lucide="chevron-down"></i></span>
                        </button>
                    </h3>
                    <div class="accordion__panel" id="faq-panel-3" role="region" aria-labelledby="faq-trigger-3"
                         x-show="isOpen(3)" x-collapse x-cloak>
                        <div class="accordion__body">
                            One authenticated POST with your image URL or binary, and a webhook
                            when the job finishes. Official SDKs cover PHP (Laravel-ready), Node
                            and Python. Most teams ship their integration in an afternoon.
                        </div>
                    </div>
                </div>

                <div class="accordion__item" :class="isOpen(4) && 'is-open'">
                    <h3>
                        <button type="button" class="accordion__trigger" @click="toggle(4)"
                                :aria-expanded="isOpen(4)" aria-controls="faq-panel-4" id="faq-trigger-4">
                            What happens if I hit my monthly credits?
                            <span class="accordion__icon" aria-hidden="true"><i data-lucide="chevron-down"></i></span>
                        </button>
                    </h3>
                    <div class="accordion__panel" id="faq-panel-4" role="region" aria-labelledby="faq-trigger-4"
                         x-show="isOpen(4)" x-collapse x-cloak>
                        <div class="accordion__body">
                            Nothing breaks. You can buy a top-up pack at any time, or enable
                            auto-recharge so pipelines never stall. There are no overage
                            surprises — we only charge what you explicitly approve.
                        </div>
                    </div>
                </div>

                <div class="accordion__item" :class="isOpen(5) && 'is-open'">
                    <h3>
                        <button type="button" class="accordion__trigger" @click="toggle(5)"
                                :aria-expanded="isOpen(5)" aria-controls="faq-panel-5" id="faq-trigger-5">
                            Can I cancel or switch plans later?
                            <span class="accordion__icon" aria-hidden="true"><i data-lucide="chevron-down"></i></span>
                        </button>
                    </h3>
                    <div class="accordion__panel" id="faq-panel-5" role="region" aria-labelledby="faq-trigger-5"
                         x-show="isOpen(5)" x-collapse x-cloak>
                        <div class="accordion__body">
                            Any time, from your billing page. Upgrades apply instantly with a
                            prorated charge; downgrades take effect at the end of the current
                            period. The first 14 days are fully refundable, no questions asked.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
