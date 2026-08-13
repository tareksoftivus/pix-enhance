<footer class="site-footer">
    <div class="shell">
        <div class="footer-grid">
            <!-- Brand column -->
            <div class="footer-brand">
                @include('frontend.themes.enhance.components.brand', ['class' => 'brand-lg'])

                <p class="footer-brand__text">
                    Production-grade AI image upscaling, restoration and enhancement.
                    Trusted by photographers, marketplaces and product teams in 90+ countries.
                </p>

                <form class="footer-newsletter" action="#" method="post">
                    <label class="sr-only" for="footer-newsletter-email">Email address</label>
                    <input class="input" type="email" id="footer-newsletter-email" name="email" placeholder="you@company.com" autocomplete="email" required>
                    <button type="submit" class="btn btn-primary btn-sm" data-ripple>
                        Subscribe
                    </button>
                </form>

                <ul class="social-row">
                    <li>
                        <a class="social-link" href="#" aria-label="PixEnhance on X">
                            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M18.24 2.25h3.31l-7.23 8.26 8.5 11.24h-6.65l-5.21-6.82-5.97 6.82H1.68l7.73-8.84L1.25 2.25h6.82l4.71 6.23 5.46-6.23Zm-1.16 17.52h1.83L7.01 4.13H5.05l12.03 15.64Z"/></svg>
                        </a>
                    </li>
                    <li>
                        <a class="social-link" href="#" aria-label="PixEnhance on GitHub">
                            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M12 2a10 10 0 0 0-3.16 19.49c.5.09.68-.22.68-.48v-1.7c-2.78.6-3.37-1.34-3.37-1.34-.45-1.16-1.11-1.47-1.11-1.47-.91-.62.07-.61.07-.61 1 .07 1.53 1.03 1.53 1.03.89 1.53 2.34 1.09 2.91.83.09-.65.35-1.09.63-1.34-2.22-.25-4.56-1.11-4.56-4.95 0-1.09.39-1.99 1.03-2.69-.1-.25-.45-1.27.1-2.65 0 0 .84-.27 2.75 1.03a9.5 9.5 0 0 1 5 0c1.91-1.3 2.75-1.03 2.75-1.03.55 1.38.2 2.4.1 2.65.64.7 1.03 1.6 1.03 2.69 0 3.85-2.34 4.7-4.57 4.95.36.31.68.92.68 1.85v2.74c0 .27.18.58.69.48A10 10 0 0 0 12 2Z"/></svg>
                        </a>
                    </li>
                    <li>
                        <a class="social-link" href="#" aria-label="PixEnhance on LinkedIn">
                            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M4.98 3.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5ZM3 9h4v12H3V9Zm7 0h3.8v1.71h.05c.53-.95 1.83-1.96 3.76-1.96 4.02 0 4.76 2.44 4.76 5.61V21h-4v-5.72c0-1.36-.03-3.12-1.94-3.12-1.94 0-2.24 1.49-2.24 3.02V21h-4V9Z"/></svg>
                        </a>
                    </li>
                    <li>
                        <a class="social-link" href="#" aria-label="PixEnhance on YouTube">
                            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M21.6 7.2a2.5 2.5 0 0 0-1.76-1.77C18.25 5 12 5 12 5s-6.25 0-7.84.43A2.5 2.5 0 0 0 2.4 7.2 26 26 0 0 0 2 12a26 26 0 0 0 .4 4.8 2.5 2.5 0 0 0 1.76 1.77C5.75 19 12 19 12 19s6.25 0 7.84-.43a2.5 2.5 0 0 0 1.76-1.77A26 26 0 0 0 22 12a26 26 0 0 0-.4-4.8ZM10 15.02V8.98L15.2 12 10 15.02Z"/></svg>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Product -->
            <div class="footer-col">
                <h2 class="footer-col__title">Product</h2>
                <ul class="footer-col__list">
                    <li><a class="footer-link" href="/upscaler">Image Upscaler</a></li>
                    <li><a class="footer-link" href="/enhancer">Image Enhancer</a></li>
                    <li><a class="footer-link" href="/background-removal">Background Removal</a></li>
                    <li><a class="footer-link" href="/face-restoration">Face Restoration</a></li>
                    <li><a class="footer-link" href="/#pricing">Pricing</a></li>
                </ul>
            </div>

            <!-- Developers -->
            <div class="footer-col">
                <h2 class="footer-col__title">Developers</h2>
                <ul class="footer-col__list">
                    <li><a class="footer-link" href="/docs">Documentation</a></li>
                    <li><a class="footer-link" href="/docs#quickstart">Quickstart</a></li>
                    <li><a class="footer-link" href="/docs#sdks">SDKs</a></li>
                    <li><a class="footer-link" href="#">Status</a></li>
                </ul>
            </div>

            <!-- Company -->
            <div class="footer-col">
                <h2 class="footer-col__title">Company</h2>
                <ul class="footer-col__list">
                    <li><a class="footer-link" href="/blog">Blog</a></li>
                    <li><a class="footer-link" href="/contact">Contact</a></li>
                    <li><a class="footer-link" href="/support">Support</a></li>
                    <li><a class="footer-link" href="#">Careers</a></li>
                    <li><a class="footer-link" href="#">Affiliates</a></li>
                </ul>
            </div>

            <!-- Legal -->
            <div class="footer-col">
                <h2 class="footer-col__title">Legal</h2>
                <ul class="footer-col__list">
                    <li><a class="footer-link" href="/terms-conditions">Terms of Service</a></li>
                    <li><a class="footer-link" href="/privacy-policy">Privacy Policy</a></li>
                    <li><a class="footer-link" href="/cookie-policy">Cookie Policy</a></li>
                    <li><a class="footer-link" href="#">GDPR</a></li>
                    <li><a class="footer-link" href="#">Licences</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; {{ $year ?? '2026' }} PixEnhance. All rights reserved.</p>

            <ul class="footer-bottom__links">
                <li><a href="#">Sitemap</a></li>
                <li><a href="#">Security</a></li>
            </ul>
        </div>
    </div>

    <div class="shell footer-watermark-fit" aria-hidden="true">
        <span class="footer-watermark">PIXENHANCE</span>
    </div>
</footer>
