@extends('layouts.guest')

@section('title', __('Reset your password'))
@section('html_class', 'no-js')
@section('body_class', '')
@section('guest_full', 'true')
@section('meta_description', __('Request a password reset link for your PixEnhance account.'))
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
                    <span class="auth__seal" aria-hidden="true"><i data-lucide="key"></i></span>
                    <h1 class="auth__title">{{ __('Forgot your password?') }}</h1>
                    <p class="auth__lead">
                        {{ __('Give us the email on the account and we will send a link to set a new password. The link is good for 60 minutes.') }}
                    </p>
                </header>

                @include('frontend.themes.enhance.components.auth-alerts', [
                    'errorTitle' => __('Unable to send reset link'),
                ])

                <form class="auth-form" action="{{ route('password.email') }}" method="post" novalidate>
                    @csrf

                    <div class="field">
                        <label class="field__label" for="forgot-email">{{ __('Email address') }}</label>
                        <div class="input-group">
                            <span class="input-group__icon" aria-hidden="true"><i data-lucide="mail"></i></span>
                            <input class="input" type="email" id="forgot-email" name="email"
                                   value="{{ old('email') }}" placeholder="{{ __('you@company.com') }}"
                                   autocomplete="email" required autofocus>
                        </div>
                        <p class="field__hint">{{ __('We will only send a link if the address matches an account.') }}</p>
                        @if($viewErrors->has('email'))
                            <p class="form-error">{{ $viewErrors->first('email') }}</p>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg btn-block btn-arrow" data-ripple>
                        {{ __('Send reset link') }}
                        <i data-lucide="arrow-right" class="icon-arrow"></i>
                    </button>
                </form>

                <div class="alert alert-info mt-lg">
                    <span class="alert__icon" aria-hidden="true"><i data-lucide="info"></i></span>
                    <div>
                        <p class="alert__title">{{ __('Signed in on another device?') }}</p>
                        <p class="alert__text">
                            {{ __('Changing your password signs out every other session, including any active API tokens.') }}
                        </p>
                    </div>
                </div>

                <p class="auth__alt">
                    {{ __('Remembered it?') }}
                    <a href="{{ route('login') }}">{{ __('Back to sign in') }}</a>
                </p>
            </div>
        </main>

        @include('frontend.themes.enhance.components.auth-foot')
    </div>

    @include('frontend.themes.enhance.components.auth-aside', [
        'eyebrow' => __('Account security'),
        'title' => __('Locked out for 60 seconds, not 60 minutes.'),
        'text' => __('Reset links land quickly and expire on first use, so a stale email in an inbox is never a way in.'),
    ])
</div>

@include('frontend.themes.enhance.components.toast-region')
@endsection
