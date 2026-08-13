<header class="site-header" data-navbar x-data="mobileNav()">
    <div class="shell">
        <nav class="navbar" aria-label="Primary">
            <!-- Brand -->
            @include('frontend.themes.enhance.components.brand')

            <!-- Desktop navigation -->
            <ul class="navbar__nav">
                <li>
                    <a class="navbar__link" href="/features">Features</a>
                </li>

                <li class="dropdown" x-data="dropdown()" @keydown.escape.window="close()" @click.outside="close()">
                    <button type="button" class="navbar__link" x-bind="trigger" aria-haspopup="true" aria-expanded="false">
                        Tools
                        <i data-lucide="chevron-down"></i>
                    </button>

                    <div class="mega-menu" x-show="open" x-cloak x-transition.origin.top.duration.220ms>
                        <div class="mega-menu__grid">
                            <a class="mega-menu__item" href="/upscaler">
                                <span class="mega-menu__icon" aria-hidden="true"><i data-lucide="maximize-2"></i></span>
                                <span>
                                    <span class="mega-menu__title">
                                        Image Upscaler
                                        <span class="badge badge-sm badge-primary">16K</span>
                                    </span>
                                    <span class="mega-menu__text">Enlarge up to 16× with zero softness or artefacts.</span>
                                </span>
                            </a>

                            <a class="mega-menu__item" href="/enhancer">
                                <span class="mega-menu__icon" aria-hidden="true"><i data-lucide="wand-sparkles"></i></span>
                                <span>
                                    <span class="mega-menu__title">Image Enhancer</span>
                                    <span class="mega-menu__text">Recover detail, colour and sharpness in one pass.</span>
                                </span>
                            </a>

                            <a class="mega-menu__item" href="/background-removal">
                                <span class="mega-menu__icon" aria-hidden="true"><i data-lucide="eraser"></i></span>
                                <span>
                                    <span class="mega-menu__title">Background Removal</span>
                                    <span class="mega-menu__text">Pixel-perfect cut-outs, hair and fur included.</span>
                                </span>
                            </a>

                            <a class="mega-menu__item" href="/face-restoration">
                                <span class="mega-menu__icon" aria-hidden="true"><i data-lucide="scan-face"></i></span>
                                <span>
                                    <span class="mega-menu__title">
                                        Face Restoration
                                        <span class="badge badge-sm badge-success">New</span>
                                    </span>
                                    <span class="mega-menu__text">Rebuild faces in old, blurred or scanned photos.</span>
                                </span>
                            </a>
                        </div>

                        <div class="mega-menu__footer">
                            <span class="text-small">Need it in your own product?</span>
                            <a class="btn-link" href="/docs">
                                Explore the API
                                <i data-lucide="arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </li>

                <li><a class="navbar__link" href="/pricing">Pricing</a></li>
                <li><a class="navbar__link" href="/docs">Docs</a></li>
                <li><a class="navbar__link" href="/blog">Blog</a></li>
            </ul>

            <!-- Desktop actions -->
            <div class="navbar__actions">
                <a class="btn btn-ghost btn-sm" href="{{ route('login') }}">Sign in</a>
                <a class="btn btn-primary btn-sm btn-arrow" href="{{ route('register') }}" data-ripple>
                    Start free
                    <i data-lucide="arrow-right" class="icon-arrow"></i>
                </a>
            </div>

            <!-- Mobile trigger -->
            <button type="button" class="navbar__burger" @click="show()" :aria-expanded="open" aria-controls="mobile-nav" aria-label="Open menu">
                <i data-lucide="menu"></i>
            </button>
        </nav>
    </div>

    <!-- Mobile drawer -->
    <div class="mobile-nav" id="mobile-nav" x-show="open" x-cloak @keydown.escape.window="close()" role="dialog" aria-modal="true" aria-label="Site menu">
        <div class="mobile-nav__scrim" x-show="open" x-transition.opacity @click="close()"></div>

        <div class="mobile-nav__panel"
             x-show="open"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full">
            <div class="mobile-nav__head">
                @include('frontend.themes.enhance.components.brand')
                <button type="button" class="btn btn-icon-sm btn-outline" @click="close()" aria-label="Close menu">
                    <i data-lucide="x"></i>
                </button>
            </div>

            <ul class="mobile-nav__list">
                <li><a class="mobile-nav__link" href="/features" @click="close()">Features</a></li>

                <li>
                    <button type="button" class="mobile-nav__link" @click="toggleSubmenu('tools')" :aria-expanded="submenu === 'tools'">
                        Tools
                        <i data-lucide="chevron-down"></i>
                    </button>
                    <ul class="mobile-nav__sublist" x-show="submenu === 'tools'" x-collapse x-cloak>
                        <li><a class="mobile-nav__sublink" href="/upscaler">Image Upscaler</a></li>
                        <li><a class="mobile-nav__sublink" href="/enhancer">Image Enhancer</a></li>
                        <li><a class="mobile-nav__sublink" href="/background-removal">Background Removal</a></li>
                        <li><a class="mobile-nav__sublink" href="/face-restoration">Face Restoration</a></li>
                    </ul>
                </li>

                <li><a class="mobile-nav__link" href="/pricing" @click="close()">Pricing</a></li>
                <li><a class="mobile-nav__link" href="/docs">Docs</a></li>
                <li><a class="mobile-nav__link" href="/blog">Blog</a></li>
                <li><a class="mobile-nav__link" href="/contact">Contact</a></li>
            </ul>

            <div class="mobile-nav__footer">
                <a class="btn btn-outline btn-block" href="{{ route('login') }}">Sign in</a>
                <a class="btn btn-primary btn-block" href="{{ route('register') }}" data-ripple>Start free — 10 credits</a>
            </div>
        </div>
    </div>
</header>
