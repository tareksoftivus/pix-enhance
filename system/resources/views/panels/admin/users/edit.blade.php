<x-layouts.admin :title="__('Edit User')">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="heading-4 text-neutral-950">{{ __('Edit User') }}</h1>
            <x-ui.button variant="outline" href="{{ route('admin.users.index') }}">
                <i class="ph ph-arrow-left"></i> {{ __('Back') }}
            </x-ui.button>
        </div>

        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="grid grid-cols-1 gap-4 lg:gap-6 2xl:grid-cols-2 space-y-4">
            @csrf
            @method('PUT')

            <div class="section-card">
                <h2 class="font-semibold text-neutral-800 mb-4">{{ __('Profile Information') }}</h2>

                <div class="space-y-5">
                    <x-media.picker :label="__('Avatar')" name="avatar" :value="$user->avatar" accept="image" :hint="__('Select an image from media library')" />

                    <x-forms.input :label="__('Name')" name="name" :value="$user->name" required :placeholder="__('Enter full name')" />
                    <x-forms.input :label="__('Email')" name="email" type="email" :value="$user->email" required :placeholder="__('Enter email address')" />
                    <x-forms.input :label="__('Phone')" name="phone" type="tel" :value="$user->phone" :placeholder="__('Enter phone number')" />
                    <x-forms.input :label="__('Password')" name="password" type="password" :placeholder="__('Leave blank to keep current password')" />
                    <x-forms.input :label="__('Confirm Password')" name="password_confirmation" type="password" :placeholder="__('Confirm password')" />
                </div>
            </div>

            <div class="space-y-4">
                <div class="section-card">
                    <h2 class="font-semibold text-neutral-800 mb-4">{{ __('Account Status') }}</h2>
                    <x-forms.toggle :label="__('Active')" name="is_active" :checked="$user->is_active" />
                </div>

                <div class="section-card">
                    <h2 class="font-semibold text-neutral-800 mb-4">{{ __('Verification Status') }}</h2>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between py-3 border-b border-neutral-100">
                            <div class="flex items-center gap-3">
                                <i class="ph ph-envelope text-xl text-neutral-500"></i>
                                <div>
                                    <p class="text-sm font-medium text-neutral-700">{{ __('Email Verification') }}</p>
                                    @if($user->email_verified_at)
                                        <p class="text-xs text-neutral-500">{{ $user->email_verified_at->format('M d, Y H:i') }}</p>
                                    @endif
                                </div>
                            </div>
                            <x-forms.toggle :label="''" name="email_verified_at" :checked="$user->email_verified_at" />
                        </div>

                        <div class="flex items-center justify-between py-3">
                            <div class="flex items-center gap-3">
                                <i class="ph ph-phone text-xl text-neutral-500"></i>
                                <div>
                                    <p class="text-sm font-medium text-neutral-700">{{ __('Phone Verification') }}</p>
                                    @if($user->phone_verified_at)
                                        <p class="text-xs text-neutral-500">{{ $user->phone_verified_at->format('M d, Y H:i') }}</p>
                                    @endif
                                </div>
                            </div>
                            <x-forms.toggle :label="''" name="phone_verified_at" :checked="$user->phone_verified_at" />
                        </div>
                    </div>
                </div>

                <div class="section-card">
                    <h2 class="font-semibold text-neutral-800 mb-4">{{ __('Two-Factor Authentication') }}</h2>

                    @if($user->hasTwoFactorEnabled())
                        <div class="flex items-center gap-2 mb-3">
                            <x-ui.badge variant="success">{{ __('Enabled') }}</x-ui.badge>
                            @if($user->hasConfirmedTwoFactor())
                                <x-ui.badge variant="light">{{ __('Confirmed') }}</x-ui.badge>
                            @else
                                <x-ui.badge variant="warning">{{ __('Not Confirmed') }}</x-ui.badge>
                            @endif
                        </div>

                        @if($user->two_factor_recovery_codes && is_array($user->two_factor_recovery_codes))
                            <div class="mb-3">
                                <p class="text-xs text-neutral-500 mb-1">{{ __('Recovery Codes:') }}</p>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($user->two_factor_recovery_codes as $code)
                                        <code class="text-xs bg-neutral-100 px-1.5 py-0.5 rounded">{{ $code }}</code>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="flex gap-2">
                            <button type="submit" name="2fa_action" value="disable" class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('Are you sure you want to disable 2FA for this user?') }}')">
                                {{ __('Disable 2FA') }}
                            </button>
                            <button type="submit" name="2fa_action" value="reset" class="btn btn-sm btn-outline-warning" onclick="return confirm('{{ __('Are you sure you want to reset 2FA for this user?') }}')">
                                {{ __('Reset 2FA') }}
                            </button>
                        </div>
                    @else
                        <div class="flex items-center gap-2">
                            <x-ui.badge variant="light">{{ __('Disabled') }}</x-ui.badge>
                            <span class="text-sm text-neutral-500">{{ __('2FA is not enabled for this user') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-neutral-100 lg:col-span-2">
                <x-forms.submit :label="__('Update User')" />
                <x-ui.button variant="ghost" href="{{ route('admin.users.index') }}">{{ __('Cancel') }}</x-ui.button>
            </div>
        </form>
    </div>
</x-layouts.admin>
