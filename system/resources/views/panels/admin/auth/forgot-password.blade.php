@extends('layouts.admin-guest')

@section('title', __('Admin Forgot Password'))

@section('content')
<div>
    <h2 class="text-3xl font-black tracking-tight text-neutral-950">{{ __('Forgot Password') }}</h2>
    <p class="mt-2 mb-8 text-sm leading-6 text-neutral-500">{{ __("Enter your admin email and we'll send you a reset link") }}</p>

    <form method="POST" action="{{ route('admin.password.email') }}" class="space-y-5">
        @csrf
        <x-forms.input :label="__('Email Address')" name="email" type="email" :value="old('email')" required placeholder="admin@example.com" icon="ph ph-envelope-simple" />
        <x-forms.submit :label="__('Send Reset Link')" class="w-full" />
    </form>

    <p class="mt-6 text-center text-sm text-neutral-400">
        {{ __('Remember your password?') }}
        <a href="{{ route('admin.login') }}" class="font-medium text-primary hover:underline">{{ __('Sign in') }}</a>
    </p>
</div>
@endsection
