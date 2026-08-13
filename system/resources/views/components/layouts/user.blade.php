@props([
    'title' => 'Dashboard',
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
</head>
<body class="bg-neutral-0 text-neutral-900 antialiased">

    {{-- Impersonation Banner --}}
    <x-ui.impersonation-banner />

    {{-- Sidebar Navigation --}}
    <x-navigation.sidebar />

    {{-- Sidebar Mobile Overlay --}}
    <div id="sidebarOverlay" class="sidebar-overlay hidden"></div>

    {{-- Main Content --}}
    <section id="mainContent" class="main-content min-h-screen">

        {{-- Topbar --}}
        <x-navigation.topbar :title="$title" />

        {{-- Page Content --}}
        <main class="p-4 lg:p-8">
            {{ $slot }}
        </main>
    </section>

    {{-- Drawer Overlay --}}
    <div id="drawerOverlay" class="drawer-overlay"></div>

    {{-- Drawers --}}
    @stack('drawers')

    {{-- Toast Notification Container --}}
    <x-ui.toast />

    {{-- Flash Messages --}}
    <x-ui.flash />

    {{-- Modals --}}
    @stack('modals')

    {{-- Additional Scripts --}}
    @stack('scripts')
</body>
</html>
