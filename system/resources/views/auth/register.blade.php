@extends('layouts.guest')

@section('title', __('Register'))

@section('content')
<div>
    <h2 class="heading-5 text-neutral-950 mb-1">{{ __('Create Account') }}</h2>
    <p class="text-sm text-neutral-400 mb-6">{{ __('Fill in the details below to get started') }}</p>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf
        <x-forms.input :label="__('Full Name')" name="name" :value="old('name')" required placeholder="John Doe" icon="ph ph-user" />
        <x-forms.input :label="__('Email Address')" name="email" type="email" :value="old('email')" required placeholder="you@example.com" icon="ph ph-envelope-simple" />
        @if (setting('require_sms_verification', false))
            <x-forms.input :label="__('Phone Number')" name="phone" type="tel" :value="old('phone')" required placeholder="+14155550100" icon="ph ph-device-mobile" />
        @endif
        <x-forms.input :label="__('Password')" name="password" type="password" required placeholder="Min. 8 characters" icon="ph ph-lock" />
        <x-forms.input :label="__('Confirm Password')" name="password_confirmation" type="password" required placeholder="Repeat password" icon="ph ph-lock" />

        <x-plugins.turnstile />

        <x-forms.submit :label="__('Create Account')" class="w-full" />
    </form>

    <x-auth.social-buttons />

    <p class="mt-6 text-center text-sm text-neutral-400">
        {{ __('Already have an account?') }}
        <a href="{{ route('login') }}" class="font-medium text-primary hover:underline">{{ __('Sign in') }}</a>
    </p>
</div>
@endsection
