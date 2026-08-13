<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $currentDirection ?? 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('Admin Login')) - {{ config('app.name', 'Admin Panel') }}</title>
    <link rel="stylesheet" href="{{ asset('assets/global/fonts/phosphor/regular/style.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('components.layouts.partials.branding')
</head>
<body class="min-h-screen bg-slate-100 text-neutral-950 antialiased">
    @php
        $siteName = setting('site_name', config('app.name', 'Admin Panel'));
        $settingLogo = setting('site_logo') && media_url(setting('site_logo')) ? media_url(setting('site_logo')) : null;
        $brandLogo = $settingLogo ?? asset('assets/uploads/brand/softivus-logo.png');
        $brandMark = $settingLogo ?? asset('assets/uploads/brand/softivus-mark.png');
        $year = now()->year;
    @endphp

    <main class="grid min-h-screen lg:grid-cols-[65fr_35fr]">
        {{-- Illustration panel --}}
        <section class="relative hidden overflow-hidden bg-[linear-gradient(160deg,#f5f9ff_0%,#eaf2ff_50%,#dfeaff_100%)] lg:flex lg:flex-col">
            <div class="relative z-10 flex h-full flex-col p-12 xl:p-14">
                {{-- Logo --}}
                <div class="flex items-center justify-between">
                    <img src="{{ $brandLogo }}" alt="{{ $siteName }}" class="h-9 w-auto max-w-52 object-contain">
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/70 px-3.5 py-1.5 text-xs font-semibold text-primary shadow-sm ring-1 ring-white/60 backdrop-blur">
                        <i class="ph ph-shield-check"></i>
                        {{ __('Secure access') }}
                    </span>
                </div>

                {{-- Illustration + headline --}}
                <div class="flex flex-1 flex-col items-center justify-center text-center">
                    <img src="{{ asset('assets/admin/images/admin-auth-illustration.svg') }}" alt="" class="h-auto w-full max-w-[34rem]">


                    <h1 class="mt-6 max-w-md text-3xl font-bold leading-tight tracking-tight text-[#123a7a] xl:text-4xl">
                        {{ __('Control the business with confidence.') }}
                    </h1>
                    <p class="mt-4 max-w-sm text-sm leading-6 text-[#2f5aa0]/80">
                        {{ __('A focused gateway for secure operations, team access, and critical business workflows.') }}
                    </p>
                </div>
            </div>
        </section>

        {{-- Auth form --}}
        <section class="relative flex min-h-screen flex-col bg-white px-5 py-10 sm:px-8">
            <div class="flex flex-1 items-center justify-center">
            <div class="relative w-full max-w-sm">
                {{-- Mobile brand --}}
                <div class="mb-8 flex items-center gap-3 lg:hidden">
                    <img src="{{ $brandMark }}" alt="{{ $siteName }}" class="h-11 w-11 rounded-2xl object-contain shadow-lg">
                    <div>
                        <h1 class="text-base font-bold text-neutral-950">{{ $siteName }}</h1>
                        <p class="text-xs text-neutral-500">{{ __('Secure admin access') }}</p>
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8 lg:border-0 lg:p-0 lg:shadow-none">
                    @if (session('success'))
                        <div class="mb-4 rounded-2xl border border-success/20 bg-success/10 p-3 text-sm font-medium text-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 rounded-2xl border border-error/20 bg-error/10 p-3 text-sm font-medium text-error">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if (session('status'))
                        <div class="mb-4 rounded-2xl border border-primary/20 bg-primary/10 p-3 text-sm font-medium text-primary">
                            {{ session('status') }}
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
            </div>

            {{-- Copyright pinned to bottom-center --}}
            <p class="mt-8 text-center text-xs text-neutral-400">
                &copy; {{ $year }} {{ $siteName }}. {{ __('All rights reserved.') }}
            </p>
        </section>
    </main>

    <div id="toastContainer" class="fixed top-4 right-4 z-[9999] flex flex-col gap-3"></div>
    <x-ui.flash />
</body>
</html>
