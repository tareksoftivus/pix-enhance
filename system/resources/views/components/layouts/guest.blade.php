@props([
    'title' => 'Welcome',
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $currentDirection ?? 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }} - {{ config('app.name', 'Admin Panel') }}</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

    {{-- Phosphor Icons --}}
    <link rel="stylesheet" href="{{ asset('assets/global/fonts/phosphor/regular/style.css') }}">

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Additional Styles --}}
    @stack('styles')

    {{-- Branding: favicon + dynamic theme colors --}}
    @include('components.layouts.partials.branding')
    <x-plugins.head-scripts />
</head>
<body class="min-h-screen flex items-center justify-center bg-neutral-50 dark:bg-neutral-0 p-4 antialiased">
    @php
        $siteName = setting('site_name', config('app.name', 'Admin Panel'));
        $settingLogo = setting('site_logo') && media_url(setting('site_logo')) ? media_url(setting('site_logo')) : null;
        $brandLogo = $settingLogo ?? asset('assets/uploads/brand/softivus-logo.png');
    @endphp

    {{-- Auth Card --}}
    <section class="section-card w-full max-w-md">

        {{-- Logo --}}
        <div class="mb-8 flex flex-col items-center gap-3">
            <img src="{{ $brandLogo }}" alt="{{ $siteName }}" class="h-12 w-auto max-w-48 object-contain">
            <h1 class="text-lg font-bold text-neutral-950">
                {{ $siteName }}
            </h1>
        </div>

        {{-- Form Content --}}
        {{ $slot }}
    </section>

    {{-- Toast Notification Container --}}
    <div id="toastContainer" class="fixed top-4 right-4 z-[9999] flex flex-col gap-3"></div>

    {{-- Flash Messages --}}
    <x-ui.flash />

    {{-- Additional Scripts --}}
    @stack('scripts')
</body>
</html>
