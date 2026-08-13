@extends('layouts.guest')

@section('title', __('Choose a new password'))
@section('html_class', 'no-js')
@section('body_class', '')
@section('guest_full', 'true')
@section('meta_description', __('Set a new password for your PixEnhance account.'))
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
    $resetEmail = old('email', $email ?? '');
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
                    <span class="auth__seal" aria-hidden="true"><i data-lucide="shield-check"></i></span>
                    <h1 class="auth__title">{{ __('Choose a new password') }}</h1>
                    <p class="auth__lead">
                        {{ __('Setting a new password for') }}
                        <strong>{{ $resetEmail ?: __('your account') }}</strong>.
                        {{ __('Every other session will be signed out.') }}
                    </p>
                </header>

                @include('frontend.themes.enhance.components.auth-alerts', [
                    'errorTitle' => __('Unable to reset password'),
                ])

                <form class="auth-form" action="{{ route('password.update') }}" method="post" novalidate>
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="field">
                        <label class="field__label" for="reset-email">{{ __('Email address') }}</label>
                        <div class="input-group">
                            <span class="input-group__icon" aria-hidden="true"><i data-lucide="mail"></i></span>
                            <input class="input" type="email" id="reset-email" name="email"
                                   value="{{ $resetEmail }}" autocomplete="email" required>
                        </div>
                        @if($viewErrors->has('email'))
                            <p class="form-error">{{ $viewErrors->first('email') }}</p>
                        @endif
                    </div>

                    <div class="field" x-data="passwordField()">
                        <label class="field__label" for="reset-password">{{ __('New password') }}</label>

                        <div class="input-group">
                            <span class="input-group__icon" aria-hidden="true"><i data-lucide="lock"></i></span>
                            <input class="input" id="reset-password" name="password"
                                   x-model="value" :type="visible ? 'text' : 'password'"
                                   placeholder="{{ __('At least 8 characters') }}" autocomplete="new-password" required autofocus>
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
                        <label class="field__label" for="reset-confirm">{{ __('Confirm new password') }}</label>
                        <div class="input-group">
                            <span class="input-group__icon" aria-hidden="true"><i data-lucide="lock"></i></span>
                            <input class="input" type="password" id="reset-confirm" name="password_confirmation"
                                   placeholder="{{ __('Type it once more') }}" autocomplete="new-password" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg btn-block btn-arrow" data-ripple>
                        {{ __('Save new password') }}
                        <i data-lucide="arrow-right" class="icon-arrow"></i>
                    </button>
                </form>

                <p class="auth__alt">
                    {{ __('Link expired?') }}
                    <a href="{{ route('password.request') }}">{{ __('Request a new one') }}</a>
                </p>
            </div>
        </main>

        @include('frontend.themes.enhance.components.auth-foot')
    </div>

    @include('frontend.themes.enhance.components.auth-aside', [
        'eyebrow' => __('Account security'),
        'title' => __('One password change, every session closed.'),
        'text' => __('Saving a new password revokes active sessions and API tokens, so a device you no longer hold loses access immediately.'),
    ])
</div>

@include('frontend.themes.enhance.components.toast-region')
@endsection
