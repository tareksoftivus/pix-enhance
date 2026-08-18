<x-layouts.user :title="__('Setup Two-Factor Authentication')" :search-placeholder="__('Search settings')">
    <div class="dash__head">
        <div>
            <h1 class="dash__title">{{ __('Setup Two-Factor Authentication') }}</h1>
            <p class="dash__subtitle">
                {{ __('Scan the QR code, save the manual key, then enter a fresh code to protect your account.') }}
            </p>
        </div>

        <div class="cluster cluster-sm">
            <a href="{{ route('user.settings') }}" class="btn btn-outline btn-sm">
                <i data-lucide="arrow-left"></i>
                {{ __('Back to settings') }}
            </a>
        </div>
    </div>

    <section class="panel" aria-labelledby="two-factor-title">
        <div class="panel__head">
            <h2 class="panel__title" id="two-factor-title">
                <i data-lucide="shield-check"></i>
                {{ __('Authenticator app') }}
            </h2>
            <span class="badge badge-sm badge-primary">{{ __('Setup') }}</span>
        </div>

        <div class="panel__body">
            <div class="form-grid form-grid-2">
                <div class="field">
                    <span class="field__label">{{ __('Step 1: Scan QR Code') }}</span>
                    <p class="field__hint">
                        {{ __('Use Google Authenticator, Authy, 1Password or another authenticator app.') }}
                    </p>

                    <div class="dropzone mt-md">
                        <span class="dropzone__icon" aria-hidden="true">
                            <i data-lucide="qr-code"></i>
                        </span>
                        <span class="dropzone__title">{{ __('Scan this code') }}</span>
                        <span class="dropzone__text">{{ __('It links your authenticator app to this PixEnhance account.') }}</span>
                        <span class="mt-md rounded-sm bg-white p-sm">
                            {!! $qrCodeSvg !!}
                        </span>
                    </div>
                </div>

                <div class="field">
                    <span class="field__label">{{ __('Manual key') }}</span>
                    <p class="field__hint">
                        {{ __('If you cannot scan the QR code, enter this key into your authenticator app manually.') }}
                    </p>

                    <div class="input-group mt-md">
                        <span class="input-group__icon" aria-hidden="true"><i data-lucide="key-round"></i></span>
                        <input class="input" type="text" value="{{ $secret }}" readonly>
                    </div>

                    <form method="POST" action="{{ route('user.two-factor.enable') }}" class="mt-lg">
                        @csrf

                        <div class="field">
                            <label class="field__label" for="two-factor-code">{{ __('Step 2: Verify Code') }}</label>
                            <div class="input-group">
                                <span class="input-group__icon" aria-hidden="true"><i data-lucide="shield-check"></i></span>
                                <input class="input" type="text" id="two-factor-code" name="code"
                                       required inputmode="numeric" autocomplete="one-time-code"
                                       placeholder="000000" autofocus>
                            </div>
                            @error('code')
                                <p class="field__hint text-danger">{{ $message }}</p>
                            @else
                                <p class="field__hint">{{ __('Enter the 6-digit code from your authenticator app.') }}</p>
                            @enderror
                        </div>

                        <div class="cluster cluster-sm mt-lg">
                            <button type="submit" class="btn btn-primary btn-sm" data-ripple>
                                <i data-lucide="shield-check"></i>
                                {{ __('Enable two-factor authentication') }}
                            </button>
                            <a href="{{ route('user.settings') }}" class="btn btn-ghost btn-sm">{{ __('Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="panel__foot">
            <p class="panel__note">
                {{ __('After enabling, you will receive recovery codes. Store them somewhere safe.') }}
            </p>
        </div>
    </section>
</x-layouts.user>
