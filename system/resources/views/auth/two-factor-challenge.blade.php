@extends('layouts.guest')

@section('title', __('Two-factor authentication'))
@section('html_class', 'no-js')
@section('body_class', '')
@section('guest_full', 'true')
@section('meta_description', __('Enter the six-digit code from your authenticator app to finish signing in to PixEnhance.'))
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
    $logoutRoute = Route::has("{$panelKey}.logout") ? route("{$panelKey}.logout") : route('logout');
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
            <div class="auth__inner" x-data="{ showRecovery: false }">
                <header class="auth__head">
                    <span class="auth__seal" aria-hidden="true"><i data-lucide="fingerprint"></i></span>
                    <h1 class="auth__title">{{ __('Two-factor check') }}</h1>
                    <p class="auth__lead" x-show="!showRecovery">
                        {{ __('Enter the six-digit code from your authenticator app. It rotates every 30 seconds.') }}
                    </p>
                    <p class="auth__lead" x-show="showRecovery" x-cloak>
                        {{ __('Enter one of the recovery codes you saved when you turned on two-factor authentication.') }}
                    </p>
                </header>

                @include('frontend.themes.enhance.components.auth-alerts', [
                    'errorTitle' => __('Unable to verify code'),
                ])

                <form class="auth-form" action="{{ route("{$panelKey}.two-factor.verify") }}"
                      method="post" x-data="otpInput(6)" x-show="!showRecovery" novalidate>
                    @csrf
                    <input type="hidden" name="code" :value="code">

                    <fieldset class="field">
                        <legend class="field__label">{{ __('Authentication code') }}</legend>

                        <div class="otp" x-ref="row" @paste="onPaste($event)">
                            <input class="input otp__input" type="text" inputmode="numeric" maxlength="1"
                                   autocomplete="one-time-code" aria-label="{{ __('Digit 1') }}"
                                   @input="onInput(0, $event)" @keydown="onKeydown(0, $event)" autofocus>
                            <input class="input otp__input" type="text" inputmode="numeric" maxlength="1"
                                   aria-label="{{ __('Digit 2') }}"
                                   @input="onInput(1, $event)" @keydown="onKeydown(1, $event)">
                            <input class="input otp__input" type="text" inputmode="numeric" maxlength="1"
                                   aria-label="{{ __('Digit 3') }}"
                                   @input="onInput(2, $event)" @keydown="onKeydown(2, $event)">

                            <span class="otp__gap" aria-hidden="true"></span>

                            <input class="input otp__input" type="text" inputmode="numeric" maxlength="1"
                                   aria-label="{{ __('Digit 4') }}"
                                   @input="onInput(3, $event)" @keydown="onKeydown(3, $event)">
                            <input class="input otp__input" type="text" inputmode="numeric" maxlength="1"
                                   aria-label="{{ __('Digit 5') }}"
                                   @input="onInput(4, $event)" @keydown="onKeydown(4, $event)">
                            <input class="input otp__input" type="text" inputmode="numeric" maxlength="1"
                                   aria-label="{{ __('Digit 6') }}"
                                   @input="onInput(5, $event)" @keydown="onKeydown(5, $event)">
                        </div>

                        <p class="field__hint">{{ __('Paste the whole code into any box and it fills the row.') }}</p>
                        @if($viewErrors->has('code'))
                            <p class="form-error">{{ $viewErrors->first('code') }}</p>
                        @endif
                    </fieldset>

                    <button type="submit" class="btn btn-primary btn-lg btn-block btn-arrow"
                            :class="!complete && 'is-disabled'" :aria-disabled="!complete" data-ripple>
                        {{ __('Verify and continue') }}
                        <i data-lucide="arrow-right" class="icon-arrow"></i>
                    </button>
                </form>

                <form class="auth-form" action="{{ route("{$panelKey}.two-factor.verify-recovery") }}"
                      method="post" x-show="showRecovery" x-cloak novalidate>
                    @csrf

                    <div class="field">
                        <label class="field__label" for="recovery-code">{{ __('Recovery code') }}</label>
                        <div class="input-group">
                            <span class="input-group__icon" aria-hidden="true"><i data-lucide="key"></i></span>
                            <input class="input" type="text" id="recovery-code" name="recovery_code"
                                   value="{{ old('recovery_code') }}" placeholder="{{ __('XXXX-XXXX') }}" required>
                        </div>
                        @if($viewErrors->has('recovery_code'))
                            <p class="form-error">{{ $viewErrors->first('recovery_code') }}</p>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg btn-block btn-arrow" data-ripple>
                        {{ __('Verify and continue') }}
                        <i data-lucide="arrow-right" class="icon-arrow"></i>
                    </button>
                </form>

                <div class="alert alert-warning mt-lg">
                    <span class="alert__icon" aria-hidden="true"><i data-lucide="triangle-alert"></i></span>
                    <div>
                        <p class="alert__title">{{ __('Lost your device?') }}</p>
                        <p class="alert__text">
                            {{ __('Use one of the recovery codes you saved when you turned on two-factor authentication.') }}
                        </p>
                        <div class="alert__actions">
                            <button type="button" class="btn-link btn-link-sm" @click="showRecovery = !showRecovery">
                                <span x-show="!showRecovery">{{ __('Use a recovery code') }}</span>
                                <span x-show="showRecovery" x-cloak>{{ __('Use an authentication code') }}</span>
                                <i data-lucide="arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <p class="auth__alt">
                    {{ __('Not you?') }}
                    <a href="{{ $logoutRoute }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        {{ __('Sign in with another account') }}
                    </a>
                </p>
                <form id="logout-form" action="{{ $logoutRoute }}" method="post" class="hidden">@csrf</form>
            </div>
        </main>

        @include('frontend.themes.enhance.components.auth-foot')
    </div>

    @include('frontend.themes.enhance.components.auth-aside', [
        'eyebrow' => __('Account security'),
        'title' => __('A password on its own is one factor too few.'),
        'text' => __('Two-factor authentication is available for every account and protects sensitive dashboard actions.'),
    ])
</div>

@include('frontend.themes.enhance.components.toast-region')
@endsection
