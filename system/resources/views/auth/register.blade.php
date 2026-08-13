@extends('layouts.guest')

@section('title', __('Create your account'))
@section('html_class', 'no-js')
@section('body_class', '')
@section('guest_full', 'true')
@section('meta_description', __('Create a free PixEnhance account and get credits to upscale, restore and enhance your images.'))
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
                    <h1 class="auth__title">{{ __('Create your account') }}</h1>
                    <p class="auth__lead">
                        {{ __('Start with free credits. No card, no trial countdown - spend them whenever you like.') }}
                    </p>
                </header>

                @include('frontend.themes.enhance.components.auth-alerts', [
                    'errorTitle' => __('Unable to create account'),
                ])

                @if($socialProviders->isNotEmpty())
                    @include('frontend.themes.enhance.components.auth-social', ['providers' => $socialProviders])
                    <p class="auth-divider">{{ __('or with email') }}</p>
                @endif

                <form class="auth-form" action="{{ route('register') }}" method="post" novalidate>
                    @csrf

                    <div class="field">
                        <label class="field__label" for="register-name">{{ __('Full name') }}</label>
                        <div class="input-group">
                            <span class="input-group__icon" aria-hidden="true"><i data-lucide="user"></i></span>
                            <input class="input" type="text" id="register-name" name="name"
                                   value="{{ old('name') }}" placeholder="{{ __('Marta Kovac') }}"
                                   autocomplete="name" required autofocus>
                        </div>
                        @if($viewErrors->has('name'))
                            <p class="form-error">{{ $viewErrors->first('name') }}</p>
                        @endif
                    </div>

                    <div class="field">
                        <label class="field__label" for="register-email">{{ __('Work email') }}</label>
                        <div class="input-group">
                            <span class="input-group__icon" aria-hidden="true"><i data-lucide="mail"></i></span>
                            <input class="input" type="email" id="register-email" name="email"
                                   value="{{ old('email') }}" placeholder="{{ __('you@company.com') }}"
                                   autocomplete="email" required>
                        </div>
                        @if($viewErrors->has('email'))
                            <p class="form-error">{{ $viewErrors->first('email') }}</p>
                        @endif
                    </div>

                    @if(setting('require_sms_verification', false))
                        <div class="field">
                            <label class="field__label" for="register-phone">{{ __('Phone number') }}</label>
                            <div class="input-group">
                                <span class="input-group__icon" aria-hidden="true"><i data-lucide="smartphone"></i></span>
                                <input class="input" type="tel" id="register-phone" name="phone"
                                       value="{{ old('phone') }}" placeholder="{{ __('+14155550100') }}"
                                       autocomplete="tel" required>
                            </div>
                            @if($viewErrors->has('phone'))
                                <p class="form-error">{{ $viewErrors->first('phone') }}</p>
                            @endif
                        </div>
                    @endif

                    <div class="field" x-data="passwordField()">
                        <label class="field__label" for="register-password">{{ __('Password') }}</label>

                        <div class="input-group">
                            <span class="input-group__icon" aria-hidden="true"><i data-lucide="lock"></i></span>
                            <input class="input" id="register-password" name="password"
                                   x-model="value" :type="visible ? 'text' : 'password'"
                                   placeholder="{{ __('At least 8 characters') }}" autocomplete="new-password" required>
                            <button type="button" class="input-group__action" @click="toggle()"
                                    aria-label="{{ __('Toggle password visibility') }}">
                                <i data-lucide="eye" x-show="!visible"></i>
                                <i data-lucide="eye-off" x-show="visible" x-cloak></i>
                            </button>
                        </div>

                        <div class="password-meter" :data-score="score" data-score="0" role="status" aria-live="polite">
                            <span class="password-meter__track" aria-hidden="true">
                                <span class="password-meter__bar" :class="score >= 1 && 'is-on'"></span>
                                <span class="password-meter__bar" :class="score >= 2 && 'is-on'"></span>
                                <span class="password-meter__bar" :class="score >= 3 && 'is-on'"></span>
                                <span class="password-meter__bar" :class="score >= 4 && 'is-on'"></span>
                            </span>
                            <span class="password-meter__label" x-text="label">&nbsp;</span>
                        </div>

                        <ul class="password-rules">
                            <li :class="rules.length && 'is-met'"><i data-lucide="check"></i>{{ __('8+ characters') }}</li>
                            <li :class="rules.case && 'is-met'"><i data-lucide="check"></i>{{ __('Upper and lowercase') }}</li>
                            <li :class="rules.digit && 'is-met'"><i data-lucide="check"></i>{{ __('A number') }}</li>
                            <li :class="rules.symbol && 'is-met'"><i data-lucide="check"></i>{{ __('A symbol') }}</li>
                        </ul>

                        @if($viewErrors->has('password'))
                            <p class="form-error">{{ $viewErrors->first('password') }}</p>
                        @endif
                    </div>

                    <div class="field">
                        <label class="field__label" for="register-confirm">{{ __('Confirm password') }}</label>
                        <div class="input-group">
                            <span class="input-group__icon" aria-hidden="true"><i data-lucide="lock"></i></span>
                            <input class="input" type="password" id="register-confirm" name="password_confirmation"
                                   placeholder="{{ __('Type it once more') }}" autocomplete="new-password" required>
                        </div>
                    </div>

                    <label class="checkbox" for="register-terms">
                        <input type="checkbox" id="register-terms" name="terms" required>
                        <span>
                            {{ __('I agree to the') }}
                            <a class="btn-link btn-link-sm" href="{{ url('/terms') }}">{{ __('Terms of Service') }}</a>
                            {{ __('and') }}
                            <a class="btn-link btn-link-sm" href="{{ url('/privacy') }}">{{ __('Privacy Policy') }}</a>.
                        </span>
                    </label>

                    <x-plugins.turnstile />

                    <button type="submit" class="btn btn-primary btn-lg btn-block btn-arrow" data-ripple>
                        {{ __('Create account') }}
                        <i data-lucide="arrow-right" class="icon-arrow"></i>
                    </button>
                </form>

                <p class="auth__alt">
                    {{ __('Already have an account?') }}
                    <a href="{{ route('login') }}">{{ __('Sign in') }}</a>
                </p>
            </div>
        </main>

        @include('frontend.themes.enhance.components.auth-foot')
    </div>

    @include('frontend.themes.enhance.components.auth-aside', [
        'eyebrow' => __('Free credits, no card'),
        'title' => __('Stop reshooting what AI can rebuild.'),
        'text' => __('Teams cut catalogue reshoots entirely - the originals they already own are enough.'),
    ])
</div>

@include('frontend.themes.enhance.components.toast-region')
@endsection
