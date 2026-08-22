@props([
    'title' => 'Dashboard',
    'searchPlaceholder' => __('Search images, jobs and projects'),
])

@php
    $siteName = setting('site_name', config('app.name', 'PixEnhance'));
    $brandLogo = setting('site_logo') && media_url(setting('site_logo'))
        ? media_url(setting('site_logo'))
        : asset('assets/frontend/enhance/img/logo.png');
    $panelKey = $panel ?? 'user';
    $currentUser = $authUser ?? auth()->user();
    $routeOr = fn (string $name, string $fallback = '#') => \Illuminate\Support\Facades\Route::has($name) ? route($name) : $fallback;
    $profileUrl = $routeOr('user.profile.edit');
    $billingUrl = $routeOr('user.billing', $profileUrl);
    $settingsUrl = $routeOr('user.settings', $profileUrl);
    $supportUrl = $routeOr('user.support-tickets.index', '#');
    $credits = $creditSummary ?? null;
    $creditBalance = (int) ($credits['balance'] ?? 0);
    $reservedCredits = (int) ($credits['reserved'] ?? 0);
    $availableCredits = (int) ($credits['available'] ?? 0);
    $creditProgress = $creditBalance > 0 ? max(0, min(100, (int) round(($availableCredits / $creditBalance) * 100))) : 0;
    $unreadNotificationCount = $currentUser
        ? app(\App\Modules\SystemNotifications\Services\SystemNotificationService::class)->getUnreadCount($currentUser)
        : 0;
@endphp

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $currentDirection ?? 'ltr' }}" class="no-js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#09090b">

    <title>{{ $title }} - {{ $siteName }}</title>
    <meta name="description" content="{{ __('Upload an image, pick a model and scale, and enhance it up to 16K.') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap">

    <link rel="stylesheet" href="{{ asset('assets/global/fonts/phosphor/regular/style.css') }}">

    @vite(['resources/css/app.css', 'resources/css/frontend/enhance/main.css', 'resources/js/app.js'])

    @stack('styles')
    @include('components.layouts.partials.branding')
</head>
<body>
    <a class="skip-link" href="#main">{{ __('Skip to content') }}</a>

    <x-ui.impersonation-banner />

    <div class="dash" x-data="{ nav: false }" @keydown.escape.window="nav = false">
        <aside class="dash__sidebar" :class="nav && 'is-open'" id="dash-nav">
            <div class="dash__brand">
                <a class="brand brand-sm" href="{{ route('user.dashboard') }}" aria-label="{{ __('Go to dashboard') }}">
                    <img class="brand__mark" src="{{ $brandLogo }}" alt="{{ $siteName }}" width="400" height="85">
                </a>

                <button type="button" class="dash__burger" @click="nav = false" aria-label="{{ __('Close menu') }}">
                    <i data-lucide="x"></i>
                </button>
            </div>

            <nav class="dash__nav" aria-label="{{ __('Dashboard') }}">
                <div class="dash__nav-group">
                    <p class="dash__nav-title">{{ __('Workspace') }}</p>
                    <ul class="dash__nav-list">
                        <li>
                            <a class="nav-item {{ request()->routeIs('user.dashboard') ? 'is-active' : '' }}" href="{{ route('user.dashboard') }}" @if(request()->routeIs('user.dashboard')) aria-current="page" @endif>
                                <i data-lucide="wand-sparkles"></i>
                                {{ __('Enhance') }}
                            </a>
                        </li>
                        <li>
                            <a class="nav-item {{ request()->routeIs('user.history') ? 'is-active' : '' }}" href="{{ route('user.history') }}" @if(request()->routeIs('user.history')) aria-current="page" @endif>
                                <i data-lucide="clock"></i>
                                {{ __('History') }}
                            </a>
                        </li>
                        <li>
                            <a class="nav-item {{ request()->routeIs('user.projects') ? 'is-active' : '' }}" href="{{ route('user.projects') }}" @if(request()->routeIs('user.projects')) aria-current="page" @endif>
                                <i data-lucide="folder"></i>
                                {{ __('Projects') }}
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="dash__nav-group">
                    <p class="dash__nav-title">{{ __('Tools') }}</p>
                    <ul class="dash__nav-list">
                        <li>
                            <a class="nav-item {{ request()->routeIs('user.upscaler') ? 'is-active' : '' }}" href="{{ route('user.upscaler') }}" @if(request()->routeIs('user.upscaler')) aria-current="page" @endif>
                                <i data-lucide="maximize-2"></i>
                                {{ __('Upscaler') }}
                            </a>
                        </li>
                        <li>
                            <a class="nav-item {{ request()->routeIs('user.face-restoration') ? 'is-active' : '' }}" href="{{ route('user.face-restoration') }}" @if(request()->routeIs('user.face-restoration')) aria-current="page" @endif>
                                <i data-lucide="scan-face"></i>
                                {{ __('Face restoration') }}
                            </a>
                        </li>
                        <li>
                            <a class="nav-item {{ request()->routeIs('user.background-removal') ? 'is-active' : '' }}" href="{{ route('user.background-removal') }}" @if(request()->routeIs('user.background-removal')) aria-current="page" @endif>
                                <i data-lucide="eraser"></i>
                                {{ __('Background removal') }}
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="dash__nav-group">
                    <p class="dash__nav-title">{{ __('Account') }}</p>
                    <ul class="dash__nav-list">
                        <li>
                            <a class="nav-item {{ request()->routeIs('user.billing') ? 'is-active' : '' }}" href="{{ $billingUrl }}" @if(request()->routeIs('user.billing')) aria-current="page" @endif>
                                <i data-lucide="credit-card"></i>
                                {{ __('Billing') }}
                            </a>
                        </li>
                        <li>
                            <a class="nav-item {{ request()->routeIs('user.settings') ? 'is-active' : '' }}" href="{{ $settingsUrl }}" @if(request()->routeIs('user.settings')) aria-current="page" @endif>
                                <i data-lucide="settings"></i>
                                {{ __('Settings') }}
                            </a>
                        </li>
                        <li>
                            <a class="nav-item {{ request()->routeIs('user.support-tickets.*') ? 'is-active' : '' }}" href="{{ $supportUrl }}" @if(request()->routeIs('user.support-tickets.*')) aria-current="page" @endif>
                                <i data-lucide="life-buoy"></i>
                                {{ __('Support') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="dash__aside-foot">
                <div class="credit-card">
                    <div class="credit-card__head">
                        <span class="credit-card__label">{{ __('Credits left') }}</span>
                        <span class="credit-card__value">{{ number_format($availableCredits) }}</span>
                    </div>

                    <div class="progress progress-sm" data-progress="{{ $creditProgress }}">
                        <div class="progress__bar"></div>
                    </div>

                    <p class="credit-card__note">
                        @if ($reservedCredits > 0)
                            {{ __(':count reserved for queued work', ['count' => number_format($reservedCredits)]) }}
                        @else
                            {{ __('Available for your next enhancement') }}
                        @endif
                    </p>

                    <a class="btn btn-primary btn-sm btn-block mt-md" href="{{ $billingUrl }}" data-ripple>
                        <i data-lucide="zap"></i>
                        {{ __('Top up credits') }}
                    </a>
                </div>
            </div>
        </aside>

        <div class="dash__scrim" x-show="nav" x-cloak x-transition.opacity @click="nav = false"></div>

        <div class="dash__main">
            <header class="dash__topbar">
                <button type="button" class="dash__burger" @click="nav = true"
                        :aria-expanded="nav" aria-controls="dash-nav" aria-label="{{ __('Open menu') }}">
                    <i data-lucide="menu"></i>
                </button>

                <form class="dash__search" action="#" method="get" role="search" data-modal-trigger="globalSearchModal">
                    <label class="sr-only" for="dash-search">{{ __('Search your images') }}</label>
                    <div class="input-group">
                        <span class="input-group__icon" aria-hidden="true"><i data-lucide="search"></i></span>
                        <input class="input" type="search" id="dash-search" name="q"
                               placeholder="{{ $searchPlaceholder }}" autocomplete="off">
                    </div>
                    <kbd aria-hidden="true">Ctrl K</kbd>
                </form>

                <div class="dash__actions">
                    <a class="icon-btn" href="{{ $routeOr('home', '/') }}" aria-label="{{ __('Documentation') }}">
                        <i data-lucide="book-open"></i>
                    </a>

                    @php
                        $bellConfig = [
                            'unreadCountUrl' => route($panelKey . '.system-notifications.unread-count'),
                            'recentUrl' => route($panelKey . '.system-notifications.recent'),
                            'markReadUrl' => route($panelKey . '.system-notifications.mark-read', ['notification' => '__ID__']),
                            'markAllReadUrl' => route($panelKey . '.system-notifications.mark-all-read'),
                            'viewAllUrl' => route($panelKey . '.system-notifications.index'),
                            'initialUnreadCount' => $unreadNotificationCount,
                        ];
                    @endphp

                    <div class="dropdown" x-data="notificationBell({{ Js::from($bellConfig) }})" @keydown.escape.window="isOpen = false" @click.outside="isOpen = false">
                        <button type="button"
                                class="icon-btn"
                                @click="togglePanel()"
                                x-bind:aria-label='unreadCount > 0 ? {{ Js::from(__('Notifications, unread items')) }} : {{ Js::from(__('Notifications')) }}'>
                            <i data-lucide="bell"></i>
                            <span class="icon-btn__dot"
                                  x-show="unreadCount > 0"
                                  @if ($unreadNotificationCount === 0) x-cloak @endif
                                  aria-hidden="true"></span>
                        </button>

                        <div class="dropdown__menu dropdown__menu-end" x-show="isOpen" x-cloak x-transition.origin.top.duration.200ms>
                            <p class="dropdown__label">{{ __('Notifications') }}</p>
                            <button type="button" class="dropdown__item" @click="markAllRead()" x-show="unreadCount > 0" x-cloak>
                                <i data-lucide="check-check"></i>
                                {{ __('Mark all read') }}
                            </button>

                            <div x-show="loading" class="dropdown__item">
                                <i data-lucide="refresh-cw"></i>
                                {{ __('Loading') }}
                            </div>

                            <template x-if="!loading">
                                <div>
                                    <template x-for="n in notifications" :key="n.id">
                                        <a :href="n.url || 'javascript:void(0)'" @click="handleNotificationClick(n, $event)" class="dropdown__item">
                                            <i data-lucide="bell"></i>
                                            <span x-text="n.title"></span>
                                        </a>
                                    </template>
                                    <div x-show="notifications.length === 0" class="dropdown__item">
                                        <i data-lucide="bell"></i>
                                        {{ __('No notifications') }}
                                    </div>
                                </div>
                            </template>

                            <hr class="dropdown__divider">

                            <a class="dropdown__item" :href="viewAllUrl">
                                <i data-lucide="arrow-right"></i>
                                {{ __('View all notifications') }}
                            </a>
                        </div>
                    </div>

                    <div class="dropdown" x-data="dropdown()" @keydown.escape.window="close()" @click.outside="close()">
                        <button type="button" class="user-chip" x-bind="trigger" aria-haspopup="true" aria-expanded="false">
                            <img src="{{ asset('assets/frontend/enhance/img/avatars/avatar-1.svg') }}" alt="" width="28" height="28">
                            <span class="user-chip__name">{{ $currentUser->name ?? __('User') }}</span>
                            <i data-lucide="chevron-down"></i>
                        </button>

                        <div class="dropdown__menu dropdown__menu-end" x-show="open" x-cloak x-transition.origin.top.duration.200ms>
                            <div class="dropdown__header">
                                <p class="dropdown__label">{{ $currentUser->name ?? __('User') }}</p>
                                <p class="dropdown__meta">{{ $currentUser->email ?? '' }}</p>
                            </div>

                            <a class="dropdown__item" href="{{ $profileUrl }}">
                                <i data-lucide="user"></i>
                                {{ __('Profile') }}
                            </a>
                            <a class="dropdown__item" href="{{ $billingUrl }}">
                                <i data-lucide="credit-card"></i>
                                {{ __('Billing & plan') }}
                            </a>
                            <a class="dropdown__item" href="{{ $settingsUrl }}">
                                <i data-lucide="settings"></i>
                                {{ __('Settings') }}
                            </a>

                            <hr class="dropdown__divider">

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown__item dropdown__item-danger">
                                    <i data-lucide="log-out"></i>
                                    {{ __('Sign out') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="dash__content" id="main">
                {{ $slot }}
            </main>
        </div>
    </div>

    <div id="drawerOverlay" class="drawer-overlay"></div>

    @stack('drawers')

    <x-ui.toast />
    @include('frontend.themes.enhance.components.toast-region')
    <x-ui.flash />
    <x-ui.global-search />
    <x-media.modal />
    <x-ui.global-confirm />

    @stack('modals')
    @stack('scripts')
</body>
</html>
