<section class="section" id="pricing" aria-labelledby="pricing-title">
    <div class="decor" aria-hidden="true">
        <span class="blob blob-lg blob-primary blob-section-center anim-drift"></span>
    </div>

    <div class="shell">
        <div x-data="pricingToggle('monthly')">
            <header class="section-head" data-reveal="up">
                <span class="badge badge-primary">
                    <i data-lucide="gem"></i>
                    Simple credit pricing
                </span>

                <h2 class="text-display-2" id="pricing-title">
                    Pay for pixels, not
                    <span class="text-gradient anim-gradient">seats</span>
                </h2>

                <p class="text-lead">
                    One credit enhances one image at any scale. Credits never expire on paid
                    plans, and every tier includes the full model catalogue.
                </p>

                <div class="billing-toggle">
                    <span class="billing-toggle__label" :class="!yearly && 'is-active'">Monthly</span>

                    <button type="button" class="switch" role="switch"
                            :aria-checked="yearly" @click="toggle()"
                            aria-label="Switch to yearly billing">
                        <span class="switch__thumb"></span>
                    </button>

                    <span class="billing-toggle__label" :class="yearly && 'is-active'">Yearly</span>
                    <span class="billing-toggle__save">Save 20%</span>
                </div>
            </header>

            <div class="pricing-grid pricing-grid-3 mt-xl">
                <!-- Starter -->
                <article class="plan" data-reveal="up" data-reveal-delay="1">
                    <header class="plan__head">
                        <h3 class="plan__name">
                            <i data-lucide="feather" class="sr-only"></i>
                            Starter
                        </h3>
                        <p class="plan__tagline">For side projects and the occasional rescue job.</p>
                    </header>

                    <div>
                        <p class="plan__price">
                            <span class="plan__currency">$</span>
                            <span class="plan__amount" x-text="yearly ? '15' : '19'">19</span>
                            <span class="plan__period">/ month</span>
                        </p>
                        <p class="plan__price-note">
                            <span x-text="yearly ? 'Billed $180 yearly' : 'Billed monthly'">Billed monthly</span>
                            <span class="plan__price-strike" x-show="yearly" x-cloak>$228</span>
                        </p>
                    </div>

                    <a class="btn btn-outline btn-block" href="{{ route('register') }}" data-ripple>
                        Start free trial
                    </a>

                    <ul class="plan__features">
                        <li class="plan__feature">
                            <span class="plan__feature-check" aria-hidden="true"><i data-lucide="check"></i></span>
                            <span><strong>300 credits</strong> per month</span>
                        </li>
                        <li class="plan__feature">
                            <span class="plan__feature-check" aria-hidden="true"><i data-lucide="check"></i></span>
                            <span>Upscale up to <strong>4×</strong> (4K output)</span>
                        </li>
                        <li class="plan__feature">
                            <span class="plan__feature-check" aria-hidden="true"><i data-lucide="check"></i></span>
                            <span>Face restoration &amp; denoise</span>
                        </li>
                        <li class="plan__feature">
                            <span class="plan__feature-check" aria-hidden="true"><i data-lucide="check"></i></span>
                            <span>Batch of 10 images</span>
                        </li>
                        <li class="plan__feature plan__feature-muted">
                            <span class="plan__feature-check" aria-hidden="true"><i data-lucide="x"></i></span>
                            <span>API access</span>
                        </li>
                    </ul>
                </article>

                <!-- Pro (featured) -->
                <article class="plan plan-featured" data-reveal="up" data-reveal-delay="2">
                    <span class="plan__ribbon">Most popular</span>

                    <header class="plan__head">
                        <h3 class="plan__name">
                            Pro
                            <span class="badge badge-sm badge-primary">16K</span>
                        </h3>
                        <p class="plan__tagline">For photographers, agencies and busy storefronts.</p>
                    </header>

                    <div>
                        <p class="plan__price">
                            <span class="plan__currency">$</span>
                            <span class="plan__amount" x-text="yearly ? '39' : '49'">49</span>
                            <span class="plan__period">/ month</span>
                        </p>
                        <p class="plan__price-note">
                            <span x-text="yearly ? 'Billed $468 yearly' : 'Billed monthly'">Billed monthly</span>
                            <span class="plan__price-strike" x-show="yearly" x-cloak>$588</span>
                        </p>
                    </div>

                    <a class="btn btn-primary btn-block btn-arrow" href="{{ route('register') }}" data-ripple>
                        Get started
                        <i data-lucide="arrow-right" class="icon-arrow"></i>
                    </a>

                    <ul class="plan__features">
                        <li class="plan__feature">
                            <span class="plan__feature-check" aria-hidden="true"><i data-lucide="check"></i></span>
                            <span><strong>1,500 credits</strong> per month</span>
                        </li>
                        <li class="plan__feature">
                            <span class="plan__feature-check" aria-hidden="true"><i data-lucide="check"></i></span>
                            <span>Upscale up to <strong>16×</strong> (16K output)</span>
                        </li>
                        <li class="plan__feature">
                            <span class="plan__feature-check" aria-hidden="true"><i data-lucide="check"></i></span>
                            <span>All nine models incl. background removal</span>
                        </li>
                        <li class="plan__feature">
                            <span class="plan__feature-check" aria-hidden="true"><i data-lucide="check"></i></span>
                            <span>Batch of <strong>200</strong> · priority GPU queue</span>
                        </li>
                        <li class="plan__feature">
                            <span class="plan__feature-check" aria-hidden="true"><i data-lucide="check"></i></span>
                            <span>API access &amp; webhooks</span>
                        </li>
                        <li class="plan__feature">
                            <span class="plan__feature-check" aria-hidden="true"><i data-lucide="check"></i></span>
                            <span>Commercial licence included</span>
                        </li>
                    </ul>
                </article>

                <!-- Scale -->
                <article class="plan" data-reveal="up" data-reveal-delay="3">
                    <header class="plan__head">
                        <h3 class="plan__name">Scale</h3>
                        <p class="plan__tagline">For marketplaces and product teams running pipelines.</p>
                    </header>

                    <div>
                        <p class="plan__price">
                            <span class="plan__currency">$</span>
                            <span class="plan__amount" x-text="yearly ? '159' : '199'">199</span>
                            <span class="plan__period">/ month</span>
                        </p>
                        <p class="plan__price-note">
                            <span x-text="yearly ? 'Billed $1,908 yearly' : 'Billed monthly'">Billed monthly</span>
                            <span class="plan__price-strike" x-show="yearly" x-cloak>$2,388</span>
                        </p>
                    </div>

                    <a class="btn btn-outline btn-block" href="/contact" data-ripple>
                        Talk to sales
                    </a>

                    <ul class="plan__features">
                        <li class="plan__feature">
                            <span class="plan__feature-check" aria-hidden="true"><i data-lucide="check"></i></span>
                            <span><strong>10,000 credits</strong> per month</span>
                        </li>
                        <li class="plan__feature">
                            <span class="plan__feature-check" aria-hidden="true"><i data-lucide="check"></i></span>
                            <span>Everything in Pro, plus:</span>
                        </li>
                        <li class="plan__feature">
                            <span class="plan__feature-check" aria-hidden="true"><i data-lucide="check"></i></span>
                            <span>Dedicated GPU capacity &amp; 99.99% SLA</span>
                        </li>
                        <li class="plan__feature">
                            <span class="plan__feature-check" aria-hidden="true"><i data-lucide="check"></i></span>
                            <span>Custom storage (S3, R2, GCS)</span>
                        </li>
                        <li class="plan__feature">
                            <span class="plan__feature-check" aria-hidden="true"><i data-lucide="check"></i></span>
                            <span>SSO, audit logs &amp; team roles</span>
                        </li>
                    </ul>
                </article>
            </div>

            <p class="text-small cluster cluster-center mt-lg" data-reveal="fade">
                <i data-lucide="shield-check"></i>
                14-day money-back guarantee · Cancel anytime ·
                <a class="btn-link" href="{{ request()->is('pricing') ? '#pricing-compare' : '/pricing#pricing-compare' }}">Compare all features</a>
            </p>
        </div>
    </div>
</section>
