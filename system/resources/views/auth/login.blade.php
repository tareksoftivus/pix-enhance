@extends('layouts.guest')

@section('title', __('Sign in'))
@section('html_class', 'no-js')
@section('body_class', '')
@section('guest_full', 'true')
@section('meta_description', __('Sign in to your PixEnhance account to upscale, restore and enhance images with AI.'))
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
    $socialProviders = collect([
        ['key' => 'google', 'label' => 'Google'],
        ['key' => 'github', 'label' => 'GitHub'],
    ])->filter(function (array $provider) {
        return (bool) setting("social_{$provider['key']}_enabled", false)
            && (string) config("services.{$provider['key']}.client_id") !== ''
            && (string) config("services.{$provider['key']}.client_secret") !== '';
    });
    $viewErrors = $errors ?? new Illuminate\Support\ViewErrorBag;
@endphp

<a class="skip-link" href="#main">{{ __('Skip to content') }}</a>

@include('frontend.themes.enhance.components.svg-defs')

<div class="auth">
    <div class="auth__panel">
        @include('frontend.themes.enhance.components.auth-bar')

        <main class="auth__body" id="main">
            <div class="auth__inner">
                <header class="auth__head">
                    <h1 class="auth__title">{{ __('Welcome back') }}</h1>
                    <p class="auth__lead">
                        {{ __('Sign in to pick up where you left off. Your queue and history are exactly where you left them.') }}
                    </p>
                </header>

                @if(session('success'))
                    <div class="alert alert-success">
                        <span class="alert__icon" aria-hidden="true"><i data-lucide="circle-check"></i></span>
                        <div>
                            <p class="alert__title">{{ __('Success') }}</p>
                            <p class="alert__text">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('status'))
                    <div class="alert alert-info">
                        <span class="alert__icon" aria-hidden="true"><i data-lucide="info"></i></span>
                        <div>
                            <p class="alert__title">{{ __('Notice') }}</p>
                            <p class="alert__text">{{ session('status') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('error') || $viewErrors->any())
                    <div class="alert alert-danger">
                        <span class="alert__icon" aria-hidden="true"><i data-lucide="circle-alert"></i></span>
                        <div>
                            <p class="alert__title">{{ __('Unable to sign in') }}</p>
                            <p class="alert__text">{{ session('error') ?: $viewErrors->first() }}</p>
                        </div>
                    </div>
                @endif

                @if($socialProviders->isNotEmpty())
                    @include('frontend.themes.enhance.components.auth-social', ['providers' => $socialProviders])
                    <p class="auth-divider">{{ __('or with email') }}</p>
                @endif

                <form class="auth-form" action="{{ route('login') }}" method="post" novalidate>
                    @csrf

                    <div class="field">
                        <label class="field__label" for="login-email">{{ __('Email address') }}</label>
                        <div class="input-group">
                            <span class="input-group__icon" aria-hidden="true"><i data-lucide="mail"></i></span>
                            <input class="input" type="email" id="login-email" name="email"
                                   value="{{ old('email') }}" placeholder="{{ __('you@company.com') }}"
                                   autocomplete="email" required autofocus>
                        </div>
                        @if($viewErrors->has('email'))
                            <p class="form-error">{{ $viewErrors->first('email') }}</p>
                        @endif
                    </div>

                    <div class="field" x-data="passwordField()">
                        <div class="field__head">
                            <label class="field__label" for="login-password">{{ __('Password') }}</label>
                            @if(Route::has('password.request'))
                                <a class="btn-link btn-link-sm" href="{{ route('password.request') }}">{{ __('Forgot?') }}</a>
                            @endif
                        </div>

                        <div class="input-group">
                            <span class="input-group__icon" aria-hidden="true"><i data-lucide="lock"></i></span>
                            <input class="input" id="login-password" name="password"
                                   x-model="value" :type="visible ? 'text' : 'password'"
                                   placeholder="{{ __('Your password') }}" autocomplete="current-password" required>
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

                    <div class="auth-form__row">
                        <label class="checkbox" for="login-remember">
                            <input type="checkbox" id="login-remember" name="remember" @checked(old('remember'))>
                            {{ __('Keep me signed in') }}
                        </label>
                    </div>

                    <x-plugins.turnstile />

                    <button type="submit" class="btn btn-primary btn-lg btn-block btn-arrow" data-ripple>
                        {{ __('Sign in') }}
                        <i data-lucide="arrow-right" class="icon-arrow"></i>
                    </button>
                </form>

                @if(Route::has('register'))
                    <p class="auth__alt">
                        {{ __('New to PixEnhance?') }}
                        <a href="{{ route('register') }}">{{ __('Create a free account') }}</a>
                    </p>
                @endif
            </div>
        </main>

        @include('frontend.themes.enhance.components.auth-foot')
    </div>

    @include('frontend.themes.enhance.components.auth-aside', [
        'title' => __('Every pixel your camera never captured.'),
        'text' => __('Upscale to 16K, restore faces and clear out noise in seconds, from any browser.'),
    ])
</div>

@include('frontend.themes.enhance.components.toast-region')
@endsection
