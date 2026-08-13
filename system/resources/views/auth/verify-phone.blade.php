@extends('layouts.guest')

@section('title', __('Verify Phone'))

@section('content')
<div class="text-center">
    <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-2xl bg-primary/10">
        <i class="ph ph-device-mobile text-3xl text-primary"></i>
    </div>

    <h2 class="heading-5 text-neutral-950 mb-2">{{ __('Verify Your Phone') }}</h2>
    <p class="text-sm text-neutral-500 mb-6">
        @if (auth()->user()->phone)
            {{ __('Enter the 6-digit code we sent to :phone.', ['phone' => auth()->user()->phone]) }}
        @else
            {{ __('Add your phone number and we\'ll text you a 6-digit verification code.') }}
        @endif
    </p>

    @if (auth()->user()->phone)
        {{-- Confirm the code --}}
        <form method="POST" action="{{ route('user.phone.verification.verify') }}" class="space-y-4 text-start">
            @csrf
            <x-forms.input :label="__('Verification Code')" name="otp" inputmode="numeric" required maxlength="6" placeholder="123456" icon="ph ph-shield-check" autocomplete="one-time-code" />
            <button type="submit" class="btn btn-primary w-full">
                <i class="ph ph-check-circle me-2"></i>
                {{ __('Verify Phone') }}
            </button>
        </form>

        {{-- Resend --}}
        <form method="POST" action="{{ route('user.phone.verification.send') }}" class="mt-4">
            @csrf
            <button type="submit" class="text-sm text-primary hover:underline">
                {{ __('Resend code') }}
            </button>
        </form>
    @else
        {{-- Set the number first --}}
        <form method="POST" action="{{ route('user.phone.verification.send') }}" class="space-y-4 text-start">
            @csrf
            <x-forms.input :label="__('Phone Number')" name="phone" type="tel" :value="old('phone')" required placeholder="+14155550100" icon="ph ph-device-mobile" />
            <button type="submit" class="btn btn-primary w-full">
                <i class="ph ph-paper-plane-tilt me-2"></i>
                {{ __('Send Verification Code') }}
            </button>
        </form>
    @endif

    <div class="mt-4 pt-4 border-t border-neutral-100">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-neutral-400 hover:text-neutral-600 transition-colors">
                {{ __('Sign out') }}
            </button>
        </form>
    </div>
</div>
@endsection
