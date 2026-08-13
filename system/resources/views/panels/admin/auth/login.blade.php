@extends('layouts.admin-guest')

@section('title', __('Admin Login'))

@section('content')
<div>
    <span class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-primary/10 text-primary">
        <i class="ph ph-lock-key text-2xl"></i>
    </span>
    <h2 class="text-3xl font-bold tracking-tight text-neutral-950">{{ __('Welcome back') }}</h2>
    <p class="mt-2 mb-8 text-sm leading-6 text-neutral-500">{{ __('Enter your admin credentials to access the panel.') }}</p>

    <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-5">
        @csrf
        <x-forms.input :label="__('Email Address')" name="email" type="email" :value="old('email')" required placeholder="admin@example.com" icon="ph ph-envelope-simple" />
        <x-forms.input :label="__('Password')" name="password" type="password" required :placeholder="__('Enter your password')" icon="ph ph-lock" />

        <div class="flex items-center justify-between pt-1">
            <x-forms.checkbox :label="__('Remember me')" name="remember" />
            <a href="{{ route('admin.password.request') }}" class="text-sm text-primary hover:underline">{{ __('Forgot password?') }}</a>
        </div>

        <x-forms.submit :label="__('Sign In')" class="w-full" />
    </form>
</div>
@endsection
