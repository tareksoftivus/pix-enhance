@php
    // Use the view-shared $panelConfig (from PanelAccess middleware) or fall back to the singleton
    $panelConfig = $panelConfig ?? app('current.panel') ?? [];
    $navigation  = $panelConfig['navigation'] ?? [];
    $siteName = setting('site_name', config('app.name', 'Admin Panel'));
    $settingLogo = setting('site_logo') && media_url(setting('site_logo')) ? media_url(setting('site_logo')) : null;
    $settingLogoDark = setting('site_logo_dark') && media_url(setting('site_logo_dark')) ? media_url(setting('site_logo_dark')) : null;
    $brandLogo = $settingLogo ?? asset('assets/uploads/brand/softivus-logo.png');
    // Dark variant: the uploaded dark logo, or the packaged inverse when the
    // packaged wordmark is in use. Null means "no swap" (custom logo only).
    $brandLogoDark = $settingLogoDark ?? ($settingLogo ? null : asset('assets/uploads/brand/softivus-logo-inverse.png'));
    $brandMark = $settingLogo ?? asset('assets/uploads/brand/softivus-mark.png');

    // Group navigation items by their 'group' key
    $groups = [];
    foreach ($navigation as $item) {
        $groupName = $item['group'] ?? 'Main Menu';
        $groups[$groupName][] = $item;
    }
@endphp

<aside id="sidebar" class="sidebar">

    {{-- Logo Section (links to the panel's dashboard) --}}
    <div class="sidebar-logo" aria-label="{{ $siteName }}">
        @php
            $panelKey = $panel ?? 'user';
            $dashboardUrl = \Illuminate\Support\Facades\Route::has("{$panelKey}.dashboard") ? route("{$panelKey}.dashboard") : url('/');
        @endphp
        <a href="{{ $dashboardUrl }}" class="flex min-w-0 items-center" aria-label="{{ __('Go to dashboard') }}">
            @if ($brandLogoDark)
                {{-- Dark variant available — swap by theme. --}}
                <img src="{{ $brandLogo }}" alt="{{ $siteName }}" class="sidebar-brand-wordmark h-10 w-auto max-w-44 shrink-0 object-contain dark:hidden"
                     data-live-logo="site_logo" data-default-src="{{ asset('assets/uploads/brand/softivus-logo.png') }}">
                <img src="{{ $brandLogoDark }}" alt="{{ $siteName }}" class="sidebar-brand-wordmark hidden h-10 w-auto max-w-44 shrink-0 object-contain dark:block"
                     data-live-logo="site_logo_dark" data-default-src="{{ asset('assets/uploads/brand/softivus-logo-inverse.png') }}">
            @else
                <img src="{{ $brandLogo }}" alt="{{ $siteName }}" class="sidebar-brand-wordmark h-10 w-auto max-w-44 shrink-0 object-contain"
                     data-live-logo="site_logo" data-default-src="{{ asset('assets/uploads/brand/softivus-logo.png') }}">
            @endif
            <img src="{{ $brandMark }}" alt="{{ $siteName }}" class="sidebar-brand-mark hidden h-10 w-10 shrink-0 object-contain"
                 data-live-logo="site_logo" data-default-src="{{ asset('assets/uploads/brand/softivus-mark.png') }}">
        </a>

        {{-- Mobile Close Button --}}
        <button type="button"
                class="ms-auto flex h-8 w-8 items-center justify-center rounded-lg text-neutral-400 hover:bg-neutral-50 hover:text-neutral-900 lg:hidden"
                data-action="close-sidebar-mobile"
                aria-label="Close Sidebar">
            <i class="ph ph-x text-lg"></i>
        </button>
    </div>

    {{-- Navigation Container --}}
    <div class="sidebar-nav-container">

        {{-- Navigation Groups --}}
        @foreach($groups as $groupTitle => $items)
            <x-navigation.sidebar-group :title="$groupTitle">
                @foreach($items as $item)
                    <x-navigation.sidebar-item
                        :label="$item['label'] ?? ''"
                        :icon="$item['icon'] ?? 'ph-circle'"
                        :route="$item['route'] ?? ''"
                        :permission="$item['permission'] ?? null"
                        :children="$item['children'] ?? []"
                    />
                @endforeach
            </x-navigation.sidebar-group>
        @endforeach

        {{-- Spacer --}}
        <div class="flex-1"></div>

        {{-- Logout Item --}}
        <div class="border-t border-neutral-100 pt-4">
            @php
                $panelKey = $panel ?? 'user';
                $logoutRoute = $panelKey === 'admin' ? route('admin.logout') : route('logout');
            @endphp
            <form method="POST" action="{{ $logoutRoute }}" id="sidebar-logout-form">
                @csrf
                <button type="submit"
                        class="nav-item w-full text-error hover:bg-error/10 hover:text-error"
                        aria-label="Logout">
                    <i class="ph ph-sign-out nav-item-icon"></i>
                    <span class="nav-item-text">{{ __('Logout') }}</span>
                </button>
            </form>
        </div>
    </div>
</aside>
