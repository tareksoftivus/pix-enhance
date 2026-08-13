<x-layouts.admin :title="__('User Details')">
    @push('scripts')
    <script>
        function impersonateUser(userId) {
            const url = '{{ route('admin.users.impersonate', ['user' => '__USER__']) }}'.replace('__USER__', userId);
            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.url) {
                    window.open(data.url, '_blank');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('{{ __('Failed to start impersonation') }}');
            });
        }
    </script>
    @endpush

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                @if($user->avatar)
                    <img src="{{ Storage::disk('public')->url($user->avatar) }}" alt="{{ $user->name }}" class="h-16 w-16 rounded-2xl object-cover ring-4 ring-neutral-100" />
                @else
                    <div class="h-16 w-16 rounded-2xl bg-primary/10 flex items-center justify-center ring-4 ring-primary/20">
                        <span class="text-2xl font-bold text-primary">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    </div>
                @endif
                <div>
                    <h1 class="heading-3 text-neutral-950">{{ $user->name }}</h1>
                    <p class="text-sm text-neutral-500 mt-0.5">{{ $user->email }}</p>
                    <div class="flex items-center gap-2 mt-2">
                        <x-ui.badge :variant="$user->is_active ? 'success' : 'danger'" class="text-xs">
                            {{ $user->is_active ? __('Active') : __('Inactive') }}
                        </x-ui.badge>
                        @if($user->roles->count() > 0)
                            @foreach($user->roles->take(2) as $role)
                                <x-ui.badge variant="info" class="text-xs">{{ $role->name }}</x-ui.badge>
                            @endforeach
                            @if($user->roles->count() > 2)
                                <x-ui.badge variant="neutral" class="text-xs">+{{ $user->roles->count() - 2 }}</x-ui.badge>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <x-ui.button variant="primary" onclick="impersonateUser({{ $user->id }})">
                    <i class="ph ph-user-switch"></i> {{ __('Login as User') }}
                </x-ui.button>
                <x-ui.button variant="outline" href="{{ route('admin.users.edit', $user) }}">
                    <i class="ph ph-pencil-simple"></i> {{ __('Edit') }}
                </x-ui.button>
                <x-ui.button variant="outline" href="{{ route('admin.users.index') }}">
                    <i class="ph ph-arrow-left"></i> {{ __('Back') }}
                </x-ui.button>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6 lg:grid-cols-4">
            <div class="stat-card group">
                <div class="flex items-start justify-between">
                    <div class="h-10 w-10 rounded-xl flex items-center justify-center bg-emerald-100 text-emerald-600 group-hover:scale-110 transition-transform">
                        @if($user->email_verified_at)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        @endif
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-xs font-medium text-neutral-400 uppercase tracking-wider">{{ __('Email') }}</p>
                    <p class="text-lg font-bold text-neutral-950 mt-1">
                        @if($user->email_verified_at)
                            <span class="text-emerald-600">{{ __('Verified') }}</span>
                        @else
                            <span class="text-amber-600">{{ __('Unverified') }}</span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="stat-card group">
                <div class="flex items-start justify-between">
                    <div class="h-10 w-10 rounded-xl flex items-center justify-center bg-blue-100 text-blue-600 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-xs font-medium text-neutral-400 uppercase tracking-wider">{{ __('2FA') }}</p>
                    <p class="text-lg font-bold text-neutral-950 mt-1">
                        @if($user->hasTwoFactorEnabled())
                            <span class="text-blue-600">{{ __('Enabled') }}</span>
                        @else
                            <span class="text-neutral-400">{{ __('Disabled') }}</span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="stat-card group">
                <div class="flex items-start justify-between">
                    <div class="h-10 w-10 rounded-xl flex items-center justify-center bg-violet-100 text-violet-600 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-xs font-medium text-neutral-400 uppercase tracking-wider">{{ __('Roles') }}</p>
                    <p class="text-lg font-bold text-neutral-950 mt-1">{{ $user->roles->count() }}</p>
                </div>
            </div>

            <div class="stat-card group">
                <div class="flex items-start justify-between">
                    <div class="h-10 w-10 rounded-xl flex items-center justify-center bg-amber-100 text-amber-600 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-xs font-medium text-neutral-400 uppercase tracking-wider">{{ __('Last Login') }}</p>
                    <p class="text-lg font-bold text-neutral-950 mt-1 truncate">
                        @if($user->last_login_at)
                            {{ $user->last_login_at->diffForHumans() }}
                        @else
                            <span class="text-neutral-400 text-sm">{{ __('Never') }}</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="section-card space-y-5">
                <h3 class="flex items-center gap-2 text-sm font-semibold tracking-wider text-neutral-500 uppercase">
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    {{ __('Personal Information') }}
                </h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between py-3 border-b border-neutral-100">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-lg bg-neutral-100 flex items-center justify-center text-neutral-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-400">{{ __('Full Name') }}</p>
                                <p class="text-sm font-medium text-neutral-900">{{ $user->name }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-3 border-b border-neutral-100">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-lg bg-neutral-100 flex items-center justify-center text-neutral-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-400">{{ __('Email Address') }}</p>
                                <p class="text-sm font-medium text-neutral-900">{{ $user->email }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-3 border-b border-neutral-100">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-lg bg-neutral-100 flex items-center justify-center text-neutral-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-400">{{ __('Phone Number') }}</p>
                                <p class="text-sm font-medium text-neutral-900">{{ $user->phone ?? __('Not provided') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-lg bg-neutral-100 flex items-center justify-center text-neutral-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-400">{{ __('Member Since') }}</p>
                                <p class="text-sm font-medium text-neutral-900">{{ format_date($user->created_at) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section-card space-y-5">
                <h3 class="flex items-center gap-2 text-sm font-semibold tracking-wider text-neutral-500 uppercase">
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    {{ __('Security & Verification') }}
                </h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between py-3 border-b border-neutral-100">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-lg bg-neutral-100 flex items-center justify-center text-neutral-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-400">{{ __('Email Verification') }}</p>
                                <div class="mt-1">
                                    @if($user->email_verified_at)
                                        <x-ui.badge variant="success" class="text-xs">{{ __('Verified') }}</x-ui.badge>
                                        <span class="text-xs text-neutral-400 ml-1">{{ format_date($user->email_verified_at, true) }}</span>
                                    @else
                                        <x-ui.badge variant="warning" class="text-xs">{{ __('Pending') }}</x-ui.badge>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-3 border-b border-neutral-100">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-lg bg-neutral-100 flex items-center justify-center text-neutral-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-400">{{ __('Two-Factor Authentication') }}</p>
                                <div class="mt-1">
                                    @if($user->hasTwoFactorEnabled())
                                        <x-ui.badge variant="success" class="text-xs">{{ __('Enabled') }}</x-ui.badge>
                                        @if($user->hasConfirmedTwoFactor())
                                            <x-ui.badge variant="info" class="text-xs ml-1">{{ __('Confirmed') }}</x-ui.badge>
                                        @endif
                                    @else
                                        <x-ui.badge variant="neutral" class="text-xs">{{ __('Disabled') }}</x-ui.badge>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-3 border-b border-neutral-100">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-lg bg-neutral-100 flex items-center justify-center text-neutral-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-400">{{ __('Account Status') }}</p>
                                <div class="mt-1">
                                    <x-ui.badge :variant="$user->is_active ? 'success' : 'danger'" class="text-xs">
                                        {{ $user->is_active ? __('Active') : __('Inactive') }}
                                    </x-ui.badge>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-lg bg-neutral-100 flex items-center justify-center text-neutral-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-400">{{ __('Assigned Roles') }}</p>
                                <div class="mt-1 flex flex-wrap gap-1">
                                    @if($user->roles->count() > 0)
                                        @foreach($user->roles as $role)
                                            <x-ui.badge variant="info" class="text-xs">{{ $role->name }}</x-ui.badge>
                                        @endforeach
                                    @else
                                        <span class="text-xs text-neutral-400 italic">{{ __('No roles') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section-card space-y-5">
                <h3 class="flex items-center gap-2 text-sm font-semibold tracking-wider text-neutral-500 uppercase">
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ __('Activity Log') }}
                </h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between py-3 border-b border-neutral-100">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-lg bg-neutral-100 flex items-center justify-center text-neutral-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-400">{{ __('Last Login') }}</p>
                                <p class="text-sm font-medium text-neutral-900">
                                    @if($user->last_login_at)
                                        {{ format_date($user->last_login_at, true) }}
                                    @else
                                        <span class="text-neutral-400 italic">{{ __('Never logged in') }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-3 border-b border-neutral-100">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-lg bg-neutral-100 flex items-center justify-center text-neutral-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-400">{{ __('Last Login IP') }}</p>
                                <p class="text-sm font-medium text-neutral-900 font-mono">
                                    {{ $user->last_login_ip ?? __('N/A') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-lg bg-neutral-100 flex items-center justify-center text-neutral-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-400">{{ __('Account Created') }}</p>
                                <p class="text-sm font-medium text-neutral-900">{{ format_date($user->created_at, true) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section-card space-y-5">
                <h3 class="flex items-center gap-2 text-sm font-semibold tracking-wider text-neutral-500 uppercase">
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ __('Additional Info') }}
                </h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between py-3 border-b border-neutral-100">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-lg bg-neutral-100 flex items-center justify-center text-neutral-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-400">{{ __('User ID') }}</p>
                                <p class="text-sm font-medium text-neutral-900 font-mono">#{{ $user->id }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-3 border-b border-neutral-100">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-lg bg-neutral-100 flex items-center justify-center text-neutral-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-400">{{ __('Last Updated') }}</p>
                                <p class="text-sm font-medium text-neutral-900">{{ format_date($user->updated_at, true) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-3 border-b border-neutral-100">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-lg bg-neutral-100 flex items-center justify-center text-neutral-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-400">{{ __('Timezone') }}</p>
                                <p class="text-sm font-medium text-neutral-900">{{ $user->created_at->timezone->name ?? __('UTC') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-lg bg-neutral-100 flex items-center justify-center text-neutral-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-400">{{ __('Deleted At') }}</p>
                                <p class="text-sm font-medium text-neutral-900">
                                    @if($user->deleted_at)
                                        {{ format_date($user->deleted_at, true) }}
                                    @else
                                        <span class="text-neutral-400 italic">{{ __('Not deleted') }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-layouts.admin>
