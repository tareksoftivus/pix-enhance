@php
    $securityErrorFields = ['current_password', 'password', 'password_confirmation'];
    $notificationErrorFields = ['render_finished', 'credits_low', 'weekly_summary', 'product_news', 'desktop_notifications_enabled', 'completion_sound_enabled'];
    $defaultsErrorFields = ['default_model', 'default_scale', 'default_format', 'face_restoration', 'auto_download', 'source_retention_days'];
    $settingsInitialTab = match (true) {
        session()->has('recovery_codes') || collect($securityErrorFields)->contains(fn (string $field) => $errors->has($field)) => 'security',
        collect($notificationErrorFields)->contains(fn (string $field) => $errors->has($field)) => 'notifications',
        collect($defaultsErrorFields)->contains(fn (string $field) => $errors->has($field)) => 'defaults',
        default => 'profile',
    };
    $workspacePreferences = $workspacePreferences ?? [];
    $notificationPreferences = $workspacePreferences['notifications'] ?? [];
    $renderDefaults = $workspacePreferences['render_defaults'] ?? [];
    $renderSummary = $renderSummary ?? [];
    $clearableJobs = ($renderSummary['completed'] ?? 0) + ($renderSummary['failed'] ?? 0) + ($renderSummary['cancelled'] ?? 0);
    $formatStorage = function (int $bytes): string {
        if ($bytes < 1024) {
            return $bytes.' B';
        }

        $units = ['KB', 'MB', 'GB', 'TB'];
        $value = $bytes / 1024;

        foreach ($units as $unit) {
            if ($value < 1024) {
                return number_format($value, $value >= 10 ? 0 : 1).' '.$unit;
            }

            $value /= 1024;
        }

        return number_format($value, 1).' PB';
    };
@endphp

<x-layouts.user :title="__('Settings')" :search-placeholder="__('Search settings')">
    <div class="dash__head">
        <div>
            <h1 class="dash__title">{{ __('Settings') }}</h1>
            <p class="dash__subtitle">
                {{ __('Your profile, sign-in security, what we email you and how renders behave by default.') }}
            </p>
        </div>
    </div>

    <div class="tabs tabs-underline" x-data="tabs(@js($settingsInitialTab))">
        <div class="tabs__list" role="tablist" aria-label="{{ __('Settings sections') }}" @keydown="onKeydown">
            @foreach ([
                ['key' => 'profile', 'icon' => 'user', 'label' => __('Profile')],
                ['key' => 'security', 'icon' => 'shield-check', 'label' => __('Security')],
                ['key' => 'notifications', 'icon' => 'bell', 'label' => __('Notifications')],
                ['key' => 'defaults', 'icon' => 'sliders-horizontal', 'label' => __('Render defaults')],
            ] as $tab)
                <button type="button" class="tabs__tab" role="tab" id="tab-{{ $tab['key'] }}"
                        :class="isActive('{{ $tab['key'] }}') && 'is-active'" :aria-selected="isActive('{{ $tab['key'] }}')"
                        :tabindex="isActive('{{ $tab['key'] }}') ? 0 : -1" aria-controls="panel-{{ $tab['key'] }}"
                        @click="select('{{ $tab['key'] }}')">
                    <i data-lucide="{{ $tab['icon'] }}"></i>
                    {{ $tab['label'] }}
                </button>
            @endforeach
        </div>

        <div class="tabs__panel" role="tabpanel" id="panel-profile" aria-labelledby="tab-profile"
             x-show="isActive('profile')" x-cloak>
            <section class="panel">
                <div class="panel__head">
                    <h2 class="panel__title">
                        <i data-lucide="user"></i>
                        {{ __('Your profile') }}
                    </h2>
                    <p class="panel__subtitle">{{ __('This is what teammates see on shared projects.') }}</p>
                </div>

                <form class="panel__body" action="{{ route('user.profile.update') }}" method="post" enctype="multipart/form-data" id="profile-form">
                    @csrf
                    @method('PUT')

                    <div class="avatar-edit">
                        <img class="avatar-edit__img"
                             src="{{ $user->avatar ? \Illuminate\Support\Facades\Storage::disk('public')->url($user->avatar) : asset('assets/frontend/enhance/img/avatars/avatar-1.svg') }}"
                             alt="" width="72" height="72">
                        <div class="avatar-edit__body">
                            <span class="setting-row__label">{{ __('Profile picture') }}</span>
                            <span class="setting-row__hint">{{ __('PNG or JPG, at least 256 × 256, up to 2 MB.') }}</span>
                            <div class="avatar-edit__actions">
                                <input class="sr-only" type="file" id="avatar-file" name="avatar" accept="image/png,image/jpeg">
                                <label class="btn btn-outline btn-sm" for="avatar-file">
                                    <i data-lucide="cloud-upload"></i>
                                    {{ __('Upload') }}
                                </label>
                            </div>
                            @error('avatar')
                                <p class="field__error"><i data-lucide="circle-alert"></i>{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <hr class="divider-fade mt-lg">

                    <div class="form-grid form-grid-2 mt-lg">
                        <div class="field form-grid__full">
                            <label class="field__label" for="set-name">{{ __('Full name') }}</label>
                            <input class="input @error('name') is-invalid @enderror" type="text" id="set-name" name="name"
                                   value="{{ old('name', $user->name) }}" autocomplete="name" required>
                            @error('name')
                                <p class="field__error"><i data-lucide="circle-alert"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="field">
                            <label class="field__label" for="set-email">{{ __('Email address') }}</label>
                            <div class="input-group">
                                <span class="input-group__icon" aria-hidden="true"><i data-lucide="mail"></i></span>
                                <input class="input @error('email') is-invalid @enderror" type="email" id="set-email" name="email"
                                       value="{{ old('email', $user->email) }}" autocomplete="email" required>
                            </div>
                            @error('email')
                                <p class="field__error"><i data-lucide="circle-alert"></i>{{ $message }}</p>
                            @else
                                <p class="field__hint">{{ __('Changing this sends a verification link to the new address.') }}</p>
                            @enderror
                        </div>

                        <div class="field">
                            <label class="field__label" for="set-phone">{{ __('Phone') }}</label>
                            <input class="input @error('phone') is-invalid @enderror" type="tel" id="set-phone" name="phone"
                                   value="{{ old('phone', $user->phone) }}" autocomplete="tel">
                            @error('phone')
                                <p class="field__error"><i data-lucide="circle-alert"></i>{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </form>

                <div class="panel__foot">
                    <p class="panel__note">{{ __('Changes apply the moment you save.') }}</p>
                    <div class="cluster cluster-sm">
                        <a class="btn btn-ghost btn-sm" href="{{ route('user.settings') }}">{{ __('Discard') }}</a>
                        <button type="submit" class="btn btn-primary btn-sm" form="profile-form" data-ripple>{{ __('Save changes') }}</button>
                    </div>
                </div>
            </section>
        </div>

        <div class="tabs__panel" role="tabpanel" id="panel-security" aria-labelledby="tab-security"
             x-show="isActive('security')" x-cloak>
            <section class="panel">
                <div class="panel__head">
                    <h2 class="panel__title">
                        <i data-lucide="lock"></i>
                        {{ __('Password') }}
                    </h2>
                </div>

                <form class="panel__body" action="{{ route('user.profile.update') }}" method="post" id="password-form">
                    @csrf
                    @method('PUT')

                    {{-- Hidden fields preserve the current profile values; this form only changes the password. --}}
                    <input type="hidden" name="name" value="{{ $user->name }}">
                    <input type="hidden" name="email" value="{{ $user->email }}">
                    <input type="hidden" name="phone" value="{{ $user->phone }}">

                    <div class="form-grid form-grid-2">
                        <div class="field form-grid__full">
                            <label class="field__label" for="set-current">{{ __('Current password') }}</label>
                            <div class="input-group">
                                <span class="input-group__icon" aria-hidden="true"><i data-lucide="lock"></i></span>
                                <input class="input @error('current_password') is-invalid @enderror" type="password" id="set-current" name="current_password"
                                       autocomplete="current-password">
                            </div>
                            @error('current_password')
                                <p class="field__error"><i data-lucide="circle-alert"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="field" x-data="passwordField()">
                            <label class="field__label" for="set-new">{{ __('New password') }}</label>
                            <div class="input-group">
                                <span class="input-group__icon" aria-hidden="true"><i data-lucide="lock"></i></span>
                                <input class="input @error('password') is-invalid @enderror" id="set-new" name="password"
                                       x-model="value" :type="visible ? 'text' : 'password'"
                                       autocomplete="new-password">
                                <button type="button" class="input-group__action" @click="toggle()"
                                        :aria-label="visible ? '{{ __('Hide password') }}' : '{{ __('Show password') }}'"
                                        aria-label="{{ __('Show password') }}">
                                    <i data-lucide="eye" x-show="!visible"></i>
                                    <i data-lucide="eye-off" x-show="visible" x-cloak></i>
                                </button>
                            </div>

                            <div class="password-meter" :data-score="score" data-score="0"
                                 role="status" aria-live="polite">
                                <span class="password-meter__track" aria-hidden="true">
                                    <span class="password-meter__bar" :class="score >= 1 && 'is-on'"></span>
                                    <span class="password-meter__bar" :class="score >= 2 && 'is-on'"></span>
                                    <span class="password-meter__bar" :class="score >= 3 && 'is-on'"></span>
                                    <span class="password-meter__bar" :class="score >= 4 && 'is-on'"></span>
                                </span>
                                <span class="password-meter__label" x-text="label">&nbsp;</span>
                            </div>
                            @error('password')
                                <p class="field__error"><i data-lucide="circle-alert"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="field">
                            <label class="field__label" for="set-confirm">{{ __('Confirm new password') }}</label>
                            <div class="input-group">
                                <span class="input-group__icon" aria-hidden="true"><i data-lucide="lock"></i></span>
                                <input class="input" type="password" id="set-confirm"
                                       name="password_confirmation" autocomplete="new-password">
                            </div>
                        </div>
                    </div>
                </form>

                <div class="panel__foot">
                    <p class="panel__note">{{ __('Saving a new password signs out every other session.') }}</p>
                    <button type="submit" class="btn btn-primary btn-sm" form="password-form" data-ripple>{{ __('Update password') }}</button>
                </div>
            </section>

            @if (setting('enable_2fa_for_users', true))
                <section class="panel">
                    <div class="panel__head">
                        <h2 class="panel__title">
                            <i data-lucide="fingerprint"></i>
                            {{ __('Two-factor authentication') }}
                        </h2>
                        @if ($user->hasTwoFactorEnabled())
                            <span class="badge badge-sm badge-success">
                                <i data-lucide="circle-check"></i>
                                {{ __('On') }}
                            </span>
                        @else
                            <span class="badge badge-sm">
                                {{ __('Off') }}
                            </span>
                        @endif
                    </div>

                    @if (session('recovery_codes'))
                        <div class="panel__body">
                            <div class="setting-row">
                                <span class="setting-row__icon">
                                    <i data-lucide="shield-alert"></i>
                                </span>
                                <span class="setting-row__text">
                                    <span class="setting-row__label">{{ __('Save these recovery codes now') }}</span>
                                    <span class="setting-row__hint">{{ __('Each code works once if you lose access to your authenticator. This is the only time they are shown.') }}</span>
                                </span>
                            </div>
                            <div class="form-grid form-grid-2 mt-md">
                                @foreach (session('recovery_codes') as $code)
                                    <code class="input" style="font-family: var(--font-mono);">{{ $code }}</code>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="panel__body">
                        @if ($user->hasTwoFactorEnabled())
                            <div class="setting-row setting-row-stack">
                                <span class="setting-row__text">
                                    <span class="setting-row__label">{{ __('Turn off two-factor authentication') }}</span>
                                    <span class="setting-row__hint">{{ __('Confirm your password to remove this extra sign-in step.') }}</span>
                                </span>
                                <form class="setting-row__control" method="post" action="{{ route('user.two-factor.disable') }}">
                                    @csrf
                                    <div class="input-group">
                                        <span class="input-group__icon" aria-hidden="true"><i data-lucide="lock"></i></span>
                                        <input class="input @error('password') is-invalid @enderror" type="password" name="password"
                                               placeholder="{{ __('Current password') }}" required>
                                    </div>
                                    @error('password')
                                        <p class="field__error"><i data-lucide="circle-alert"></i>{{ $message }}</p>
                                    @enderror
                                    <button type="submit" class="btn btn-danger-soft btn-sm mt-md">{{ __('Turn off') }}</button>
                                </form>
                            </div>
                        @else
                            <div class="setting-row">
                                <span class="setting-row__text">
                                    <span class="setting-row__label">{{ __('Authenticator app') }}</span>
                                    <span class="setting-row__hint">{{ __('Not enabled. Add an authenticator app for a second sign-in step.') }}</span>
                                </span>
                                <span class="setting-row__control">
                                    <a class="btn btn-outline btn-sm" href="{{ route('user.two-factor.setup') }}">
                                        {{ __('Enable') }}
                                    </a>
                                </span>
                            </div>
                        @endif
                    </div>
                </section>
            @endif

            <section class="panel">
                <div class="panel__head">
                    <h2 class="panel__title">
                        <i data-lucide="monitor"></i>
                        {{ __('Active sessions') }}
                    </h2>
                    @if ($sessions->where('is_current', false)->isNotEmpty())
                        <form method="post" action="{{ route('user.profile.sessions.revoke-all') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline btn-sm">
                                <i data-lucide="log-out"></i>
                                {{ __('Sign out everywhere') }}
                            </button>
                        </form>
                    @endif
                </div>

                <div class="panel__body">
                    <div class="session-list">
                        @forelse ($sessions as $session)
                            <div class="session">
                                <span class="session__icon" aria-hidden="true">
                                    <i data-lucide="{{ $session->device === 'Mobile' ? 'smartphone' : ($session->device === 'Tablet' ? 'tablet' : 'monitor') }}"></i>
                                </span>
                                <span class="session__body">
                                    <span class="session__device">
                                        {{ $session->browser }} — {{ $session->platform }}
                                        @if ($session->is_current)
                                            <span class="badge badge-sm badge-success">{{ __('This device') }}</span>
                                        @endif
                                    </span>
                                    <span class="session__meta">
                                        {{ $session->ip_address }} &middot; {{ $session->last_activity->diffForHumans() }}
                                    </span>
                                </span>
                                @unless ($session->is_current)
                                    <form method="post" action="{{ route('user.profile.sessions.revoke', $session->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-ghost btn-sm">{{ __('Revoke') }}</button>
                                    </form>
                                @endunless
                            </div>
                        @empty
                            <p class="setting-row__hint">{{ __('No active sessions found.') }}</p>
                        @endforelse
                    </div>
                </div>
            </section>

            <section class="panel danger-panel">
                <div class="panel__head">
                    <h2 class="panel__title">
                        <i data-lucide="triangle-alert"></i>
                        {{ __('Delete account') }}
                    </h2>
                </div>

                <div class="panel__body">
                    <p class="setting-row__hint">
                        {{ __('This removes your account, every stored image and all render history. Invoices are retained for seven years for tax purposes. There is no undo.') }}
                    </p>
                </div>

                <div class="panel__foot">
                    <p class="panel__note">{{ __('Account deletion is not available yet — contact support to close your account.') }}</p>
                    <a class="btn btn-danger btn-sm" href="{{ route('user.support-tickets.create') }}">
                        <i data-lucide="life-buoy"></i>
                        {{ __('Contact support') }}
                    </a>
                </div>
            </section>
        </div>

        <div class="tabs__panel" role="tabpanel" id="panel-notifications" aria-labelledby="tab-notifications"
             x-show="isActive('notifications')" x-cloak>
            <section class="panel">
                <div class="panel__head">
                    <h2 class="panel__title">
                        <i data-lucide="mail"></i>
                        {{ __('Email') }}
                    </h2>
                    <p class="panel__subtitle">{{ __('Sent to :email.', ['email' => $user->email]) }}</p>
                </div>

                <form class="panel__body" action="{{ route('user.workspace.notifications.update') }}" method="post" id="notification-preferences-form">
                    @csrf
                    @method('PUT')

                    @foreach ([
                        ['name' => 'render_finished', 'label' => __('Render finished'), 'hint' => __('Only for batches over 20 images — single renders are usually done before the email lands.'), 'sr' => __('Email me when a render finishes')],
                        ['name' => 'credits_low', 'label' => __('Credits running low'), 'hint' => __('A single warning when you drop below 10% of your monthly allowance.'), 'sr' => __('Email me when credits run low')],
                        ['name' => 'weekly_summary', 'label' => __('Weekly summary'), 'hint' => __('Renders, credits spent and storage used, every Monday.'), 'sr' => __('Send me a weekly summary')],
                        ['name' => 'product_news', 'label' => __('Product news'), 'hint' => __('New models and features. Roughly once a month, never more.'), 'sr' => __('Send me product news')],
                    ] as $item)
                        <div class="setting-row">
                            <span class="setting-row__text">
                                <span class="setting-row__label">{{ $item['label'] }}</span>
                                <span class="setting-row__hint">{{ $item['hint'] }}</span>
                            </span>
                            <span class="setting-row__control">
                                <label class="switch-field">
                                    <input type="hidden" name="{{ $item['name'] }}" value="0">
                                    <input class="switch-field__input" type="checkbox" name="{{ $item['name'] }}" value="1" @checked((bool) ($notificationPreferences[$item['name']] ?? false))>
                                    <span class="switch-field__track"></span>
                                    <span class="sr-only">{{ $item['sr'] }}</span>
                                </label>
                            </span>
                        </div>
                    @endforeach
                </form>

                <div class="panel__foot">
                    <p class="panel__note">{{ __('Email preferences are used by render, credit and product updates.') }}</p>
                    <button type="submit" class="btn btn-primary btn-sm" form="notification-preferences-form" data-ripple>{{ __('Save notifications') }}</button>
                </div>
            </section>

            <section class="panel">
                <div class="panel__head">
                    <h2 class="panel__title">
                        <i data-lucide="bell"></i>
                        {{ __('In-app') }}
                    </h2>
                </div>

                <div class="panel__body">
                    <div class="setting-row">
                        <span class="setting-row__text">
                            <span class="setting-row__label">{{ __('Desktop notifications') }}</span>
                            <span class="setting-row__hint">{{ __('Requires permission from your browser.') }}</span>
                        </span>
                        <span class="setting-row__control">
                            <label class="switch-field">
                                <input type="hidden" name="desktop_notifications_enabled" value="0" form="notification-preferences-form">
                                <input class="switch-field__input" type="checkbox" name="desktop_notifications_enabled" value="1" form="notification-preferences-form" @checked((bool) ($workspacePreferences['desktop_notifications_enabled'] ?? false))>
                                <span class="switch-field__track"></span>
                                <span class="sr-only">{{ __('Enable desktop notifications') }}</span>
                            </label>
                        </span>
                    </div>

                    <div class="setting-row">
                        <span class="setting-row__text">
                            <span class="setting-row__label">{{ __('Sound on completion') }}</span>
                            <span class="setting-row__hint">{{ __('A short chime when a render lands.') }}</span>
                        </span>
                        <span class="setting-row__control">
                            <label class="switch-field">
                                <input type="hidden" name="completion_sound_enabled" value="0" form="notification-preferences-form">
                                <input class="switch-field__input" type="checkbox" name="completion_sound_enabled" value="1" form="notification-preferences-form" @checked((bool) ($workspacePreferences['completion_sound_enabled'] ?? false))>
                                <span class="switch-field__track"></span>
                                <span class="sr-only">{{ __('Play a sound on completion') }}</span>
                            </label>
                        </span>
                    </div>
                </div>
            </section>
        </div>

        <div class="tabs__panel" role="tabpanel" id="panel-defaults" aria-labelledby="tab-defaults"
             x-show="isActive('defaults')" x-cloak>
            <section class="panel">
                <div class="panel__head">
                    <h2 class="panel__title">
                        <i data-lucide="sliders-horizontal"></i>
                        {{ __('Studio defaults') }}
                    </h2>
                    <p class="panel__subtitle">{{ __('What every new render starts with. You can still change it per job.') }}</p>
                </div>

                <form class="panel__body" action="{{ route('user.workspace.render-defaults.update') }}" method="post" id="render-defaults-form">
                    @csrf
                    @method('PUT')

                    <div class="setting-row">
                        <span class="setting-row__text">
                            <span class="setting-row__label">{{ __('Default model') }}</span>
                            <span class="setting-row__hint">{{ __('Auto picks a model from what it finds in the image.') }}</span>
                        </span>
                        <span class="setting-row__control">
                            <label class="sr-only" for="def-model">{{ __('Default model') }}</label>
                            <select class="select input-sm" id="def-model" name="default_model">
                                <option value="auto" @selected(($renderDefaults['default_model'] ?? 'auto') === 'auto')>{{ __('Auto') }}</option>
                                <option value="enhance-xl" @selected(($renderDefaults['default_model'] ?? 'auto') === 'enhance-xl')>{{ __('Enhance-XL v3') }}</option>
                                <option value="photo-real" @selected(($renderDefaults['default_model'] ?? 'auto') === 'photo-real')>{{ __('Photo Real v2') }}</option>
                                <option value="illustration" @selected(($renderDefaults['default_model'] ?? 'auto') === 'illustration')>{{ __('Illustration v1') }}</option>
                            </select>
                        </span>
                    </div>

                    <div class="setting-row">
                        <span class="setting-row__text">
                            <span class="setting-row__label">{{ __('Default scale') }}</span>
                            <span class="setting-row__hint">{{ __('Higher scales cost more credits per render.') }}</span>
                        </span>
                        <span class="setting-row__control">
                            <span class="radio-group">
                                @foreach (['2', '4', '8'] as $scale)
                                    <span class="radio-group__option">
                                        <input class="radio-group__input" type="radio" id="def-scale-{{ $scale }}" name="default_scale" value="{{ $scale }}" @checked((string) ($renderDefaults['default_scale'] ?? '4') === $scale)>
                                        <label class="radio-group__label" for="def-scale-{{ $scale }}">{{ $scale }}×</label>
                                    </span>
                                @endforeach
                            </span>
                        </span>
                    </div>

                    <div class="setting-row">
                        <span class="setting-row__text">
                            <span class="setting-row__label">{{ __('Default output format') }}</span>
                            <span class="setting-row__hint">{{ __('PNG and TIFF are lossless; JPG and WEBP are smaller.') }}</span>
                        </span>
                        <span class="setting-row__control">
                            <label class="sr-only" for="def-format">{{ __('Default output format') }}</label>
                            <select class="select input-sm" id="def-format" name="default_format">
                                @foreach (['png', 'jpg', 'webp', 'tiff'] as $format)
                                    <option value="{{ $format }}" @selected(($renderDefaults['default_format'] ?? 'png') === $format)>{{ strtoupper($format) }}</option>
                                @endforeach
                            </select>
                        </span>
                    </div>

                    @foreach ([
                        ['name' => 'face_restoration', 'label' => __('Face restoration'), 'hint' => __('Runs automatically whenever a face is detected.'), 'sr' => __('Enable face restoration by default')],
                        ['name' => 'auto_download', 'label' => __('Download when finished'), 'hint' => __('Saves the result to your device as soon as it is ready.'), 'sr' => __('Download automatically when a render finishes')],
                    ] as $item)
                        <div class="setting-row">
                            <span class="setting-row__text">
                                <span class="setting-row__label">{{ $item['label'] }}</span>
                                <span class="setting-row__hint">{{ $item['hint'] }}</span>
                            </span>
                            <span class="setting-row__control">
                                <label class="switch-field">
                                    <input type="hidden" name="{{ $item['name'] }}" value="0">
                                    <input class="switch-field__input" type="checkbox" name="{{ $item['name'] }}" value="1" @checked((bool) ($renderDefaults[$item['name']] ?? false))>
                                    <span class="switch-field__track"></span>
                                    <span class="sr-only">{{ $item['sr'] }}</span>
                                </label>
                            </span>
                        </div>
                    @endforeach
                </form>

                <div class="panel__foot">
                    <p class="panel__note">{{ __('Defaults apply to new renders only.') }}</p>
                    <button type="submit" class="btn btn-primary btn-sm" form="render-defaults-form" data-ripple>{{ __('Save defaults') }}</button>
                </div>
            </section>

            <section class="panel">
                <div class="panel__head">
                    <h2 class="panel__title">
                        <i data-lucide="database"></i>
                        {{ __('Storage') }}
                    </h2>
                    <p class="panel__subtitle">{{ __('Combined size of your uploaded sources and rendered outputs.') }}</p>
                </div>

                <div class="panel__body">
                    <div class="setting-row">
                        <span class="setting-row__text">
                            <span class="setting-row__label">{{ __('Storage used') }}</span>
                            <span class="setting-row__hint">{{ __(':count render jobs on file.', ['count' => number_format($renderSummary['total'] ?? 0)]) }}</span>
                        </span>
                        <span class="setting-row__control">
                            <strong>{{ $formatStorage((int) ($renderSummary['storage_bytes'] ?? 0)) }}</strong>
                        </span>
                    </div>

                    <div class="setting-row mt-lg">
                        <span class="setting-row__text">
                            <span class="setting-row__label">{{ __('Keep originals for') }}</span>
                            <span class="setting-row__hint">{{ __('Source files are deleted after this window. Results are kept until you remove them.') }}</span>
                        </span>
                        <span class="setting-row__control">
                            <label class="sr-only" for="def-retention">{{ __('Keep originals for') }}</label>
                            <select class="select input-sm" id="def-retention" name="source_retention_days" form="render-defaults-form">
                                @foreach ([1 => __('24 hours'), 7 => __('7 days'), 30 => __('30 days'), 90 => __('90 days')] as $days => $label)
                                    <option value="{{ $days }}" @selected((int) ($workspacePreferences['source_retention_days'] ?? 7) === $days)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </span>
                    </div>

                    <div class="setting-row">
                        <span class="setting-row__text">
                            <span class="setting-row__label">{{ __('Clear render history') }}</span>
                            <span class="setting-row__hint">
                                {{ $clearableJobs > 0
                                    ? __('Permanently removes :count finished, failed or cancelled render job(s) from your history. Jobs still in progress are kept.', ['count' => number_format($clearableJobs)])
                                    : __('No finished render jobs to clear yet.') }}
                            </span>
                        </span>
                        <span class="setting-row__control">
                            <form method="post" action="{{ route('user.render-jobs.clear-history') }}"
                                  onsubmit="return confirm('{{ __('Permanently clear your finished render history? This cannot be undone.') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger-soft btn-sm" @disabled($clearableJobs === 0)>
                                    <i data-lucide="trash-2"></i>
                                    {{ __('Clear history') }}
                                </button>
                            </form>
                        </span>
                    </div>
                </div>

                <div class="panel__foot">
                    <p class="panel__note">{{ __('Retention applies to source files uploaded after saving.') }}</p>
                    <button type="submit" class="btn btn-primary btn-sm" form="render-defaults-form" data-ripple>{{ __('Save storage') }}</button>
                </div>
            </section>
        </div>
    </div>
</x-layouts.user>
