@extends('layouts.guest')

@section('title', __('Verify your email'))
@section('html_class', 'no-js')
@section('body_class', '')
@section('guest_full', 'true')
@section('meta_description', __('Confirm your email address to activate your PixEnhance account.'))
@section('meta_robots', 'noindex, nofollow')
@section('theme_color', '#09090b')

@section('guest_assets')
    @vite('resources/js/frontend/enhance/main.js')
@endsection

@section('head')
    <link rel="icon" href="{{ asset('assets/frontend/enhance/favicon.png') }}" type="image/png" sizes="96x96">
    <link rel="apple-touch-icon" href="{{ asset('assets/frontend/enhance/apple-touch-icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap">
@endsection

@section('content')
@php
    $userEmail = auth()->user()?->email;
@endphp

<a class="skip-link" href="#main">{{ __('Skip to content') }}</a>

@include('frontend.themes.enhance.components.svg-defs')

<div class="auth">
    <div class="auth__panel">
        @include('frontend.themes.enhance.components.auth-bar', [
            'backHref' => route('login'),
            'backLabel' => __('Back to sign in'),
        ])

        <main class="auth__body" id="main">
            <div class="auth__inner">
                <header class="auth__head">
                    <span class="auth__seal auth__seal-accent" aria-hidden="true"><i data-lucide="mail"></i></span>
                    <h1 class="auth__title">{{ __('Check your inbox') }}</h1>
                    <p class="auth__lead">
                        {{ __('We sent a verification link to') }}
                        <strong>{{ $userEmail ?: __('your email address') }}</strong>.
                        {{ __('Open it and your free credits are ready to spend.') }}
                    </p>
                </header>

                @include('frontend.themes.enhance.components.auth-alerts', [
                    'errorTitle' => __('Unable to resend verification email'),
                ])

                <div class="alert alert-info">
                    <span class="alert__icon" aria-hidden="true"><i data-lucide="clock"></i></span>
                    <div>
                        <p class="alert__title">{{ __('Nothing yet?') }}</p>
                        <p class="alert__text">
                            {{ __('Delivery usually takes under a minute. Check spam before resending - a second link invalidates the first.') }}
                        </p>
                    </div>
                </div>

                <form class="auth-form mt-lg" action="{{ route('verification.send') }}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-lg btn-block" data-ripple>
                        <i data-lucide="refresh-cw"></i>
                        {{ __('Resend verification email') }}
                    </button>
                </form>

                <p class="auth__alt">
                    {{ __('Wrong account?') }}
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('verify-logout-form').submit();">
                        {{ __('Sign out and start again') }}
                    </a>
                </p>
                <form id="verify-logout-form" action="{{ route('logout') }}" method="post" class="hidden">@csrf</form>
            </div>
        </main>

        @include('frontend.themes.enhance.components.auth-foot')
    </div>

    @include('frontend.themes.enhance.components.auth-aside', [
        'eyebrow' => __('One click away'),
        'title' => __('Verified accounts get the full free-credit start.'),
        'text' => __('Confirming your address keeps the queue clean and unlocks batch uploads and the API.'),
    ])
</div>

@include('frontend.themes.enhance.components.toast-region')
@endsection
