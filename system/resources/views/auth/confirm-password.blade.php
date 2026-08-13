@extends('layouts.guest')

@section('title', __('Confirm your password'))
@section('html_class', 'no-js')
@section('body_class', '')
@section('guest_full', 'true')
@section('meta_description', __('Re-enter your password to continue to a protected area of your PixEnhance account.'))
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
    $viewErrors = $errors ?? new Illuminate\Support\ViewErrorBag;
    $dashboardRoute = Route::has('user.dashboard') ? route('user.dashboard') : route('home');
@endphp

<a class="skip-link" href="#main">{{ __('Skip to content') }}</a>

@include('frontend.themes.enhance.components.svg-defs')

<div class="auth">
    <div class="auth__panel">
        @include('frontend.themes.enhance.components.auth-bar', [
            'backHref' => $dashboardRoute,
            'backLabel' => __('Back to dashboard'),
        ])

        <main class="auth__body" id="main">
            <div class="auth__inner">
                <header class="auth__head">
                    <span class="auth__seal" aria-hidden="true"><i data-lucide="shield-check"></i></span>
                    <h1 class="auth__title">{{ __('Confirm it is you') }}</h1>
                    <p class="auth__lead">
                        {{ __('This is a protected area. Re-enter your password to continue - we will not ask again for another three hours.') }}
                    </p>
                </header>

                @include('frontend.themes.enhance.components.auth-alerts', [
                    'errorTitle' => __('Unable to confirm password'),
                ])

                <form class="auth-form" action="{{ route('password.confirm') }}" method="post" novalidate>
                    @csrf

                    <div class="field" x-data="passwordField()">
                        <div class="field__head">
                            <label class="field__label" for="confirm-password">{{ __('Password') }}</label>
                            @if(Route::has('password.request'))
                                <a class="btn-link btn-link-sm" href="{{ route('password.request') }}">{{ __('Forgot?') }}</a>
                            @endif
                        </div>

                        <div class="input-group">
                            <span class="input-group__icon" aria-hidden="true"><i data-lucide="lock"></i></span>
                            <input class="input" id="confirm-password" name="password"
                                   x-model="value" :type="visible ? 'text' : 'password'"
                                   placeholder="{{ __('Your password') }}" autocomplete="current-password" required autofocus>
                            <button type="button" class="input-group__action" @click="toggle()"
                                    aria-label="{{ __('Toggle password visibility') }}">
                                <i data-lucide="eye" x-show="!visible"></i>
                                <i data-lucide="eye-off" x-show="visible" x-cloak></i>
                            </button>
                        </div>
                        @if($viewErrors->has('password'))
                            <p class="form-error">{{ $viewErrors->first('password') }}</p>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg btn-block btn-arrow" data-ripple>
                        {{ __('Confirm') }}
                        <i data-lucide="arrow-right" class="icon-arrow"></i>
                    </button>
                </form>

                <p class="auth__alt">
                    {{ __('Changed your mind?') }}
                    <a href="{{ $dashboardRoute }}">{{ __('Return to the dashboard') }}</a>
                </p>
            </div>
        </main>

        @include('frontend.themes.enhance.components.auth-foot')
    </div>

    @include('frontend.themes.enhance.components.auth-aside', [
        'eyebrow' => __('Account security'),
        'title' => __('Billing and account changes ask twice.'),
        'text' => __('Anything that can move money or change who has access sits behind a second password check, even on a trusted device.'),
    ])
</div>

@include('frontend.themes.enhance.components.toast-region')
@endsection
