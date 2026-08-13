@extends('layouts.guest')

@section('title', __('Login'))

@section('content')
<div>
    <h2 class="heading-5 text-neutral-950 mb-1">{{ __('Sign In') }}</h2>
    <p class="text-sm text-neutral-400 mb-6">{{ __('Enter your credentials to access your account') }}</p>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <x-forms.input :label="__('Email Address')" name="email" type="email" :value="old('email')" required placeholder="you@example.com" icon="ph ph-envelope-simple" />
        <x-forms.input :label="__('Password')" name="password" type="password" required :placeholder="__('Enter your password')" icon="ph ph-lock" />

        <div class="flex items-center justify-between">
            <x-forms.checkbox :label="__('Remember me')" name="remember" />
            @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="text-sm text-primary hover:underline">{{ __('Forgot password?') }}</a>
            @endif
        </div>

        <x-plugins.turnstile />

        <x-forms.submit :label="__('Sign In')" class="w-full" />
    </form>

    <x-auth.social-buttons />

    @if (Route::has('register'))
    <p class="mt-6 text-center text-sm text-neutral-400">
        {{ __("Don't have an account?") }}
        <a href="{{ route('register') }}" class="font-medium text-primary hover:underline">{{ __('Sign up') }}</a>
    </p>
    @endif
</div>
@endsection
