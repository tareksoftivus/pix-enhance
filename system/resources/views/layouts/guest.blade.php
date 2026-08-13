<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Welcome') - {{ config('app.name', 'Admin Panel') }}</title>
    <link rel="stylesheet" href="{{ asset('assets/global/fonts/phosphor/regular/style.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('components.layouts.partials.branding')
    <x-plugins.head-scripts />
</head>
<body class="min-h-screen flex items-center justify-center bg-neutral-50 dark:bg-neutral-0 p-4 antialiased">
    @php
        $siteName = setting('site_name', config('app.name', 'Admin Panel'));
        $settingLogo = setting('site_logo') && media_url(setting('site_logo')) ? media_url(setting('site_logo')) : null;
        // Prefer the uploaded site logo (a wide wordmark); it renders at its
        // natural aspect. Only the packaged square mark uses the rounded badge.
        $brandLogo = $settingLogo ?? asset('assets/uploads/brand/softivus-logo.png');
    @endphp

    <div class="section-card w-full max-w-md">
        {{-- Logo (the wordmark already carries the brand name). --}}
        <div class="mb-8 flex justify-center">
            <img src="{{ $brandLogo }}" alt="{{ $siteName }}" class="h-12 w-auto max-w-48 object-contain">
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-xl border border-success/30 bg-success/10 p-3 text-sm text-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 rounded-xl border border-error/30 bg-error/10 p-3 text-sm text-error">
                {{ session('error') }}
            </div>
        @endif

        @if (session('status'))
            <div class="mb-4 rounded-xl border border-primary/30 bg-primary/10 p-3 text-sm text-primary">
                {{ session('status') }}
            </div>
        @endif

        @yield('content')
    </div>

    <div id="toastContainer" class="fixed top-4 right-4 z-[9999] flex flex-col gap-3"></div>
    <x-ui.flash />
</body>
</html>
