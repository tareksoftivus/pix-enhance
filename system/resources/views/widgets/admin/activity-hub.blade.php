@php
    // Map payment status to a badge variant + row icon colour.
    $statusMeta = [
        'completed' => ['variant' => 'success', 'tint' => 'bg-success/10 text-success'],
        'pending' => ['variant' => 'warning', 'tint' => 'bg-warning/10 text-warning'],
        'failed' => ['variant' => 'danger', 'tint' => 'bg-error/10 text-error'],
    ];

    // Tab definitions: label, count and the module's index route for "View All".
    $tabs = [
        'payments' => ['label' => __('Payments'), 'icon' => 'ph-credit-card', 'count' => $recentPayments->count(), 'route' => Route::has('admin.payments.index') ? route('admin.payments.index') : null],
        'users' => ['label' => __('Users'), 'icon' => 'ph-users', 'count' => $recentUsers->count(), 'route' => Route::has('admin.users.index') ? route('admin.users.index') : null],
        'logins' => ['label' => __('Logins'), 'icon' => 'ph-activity', 'count' => $loginActivities->count(), 'route' => Route::has('admin.login-activity.index') ? route('admin.login-activity.index') : null],
    ];
@endphp

<div class="section-card" x-data="{ tab: 'payments' }">
    <h2 class="heading-5 text-neutral-950">{{ __('Recent Activity') }}</h2>

    <div class="mt-4 mb-5 flex items-center justify-between gap-3">
        <div class="flex items-center gap-1 rounded-xl bg-neutral-100 p-1">
            @foreach($tabs as $key => $meta)
                <button
                    type="button"
                    x-on:click="tab = '{{ $key }}'"
                    x-bind:class="tab === '{{ $key }}' ? 'bg-neutral-0 text-neutral-950 shadow-sm' : 'text-neutral-500'"
                    class="flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-sm font-medium transition-colors"
                >
                    <i class="ph {{ $meta['icon'] }}"></i>
                    {{ $meta['label'] }}
                </button>
            @endforeach
        </div>

        @foreach($tabs as $key => $meta)
            @if($meta['route'])
                <a href="{{ $meta['route'] }}" x-show="tab === '{{ $key }}'" x-cloak class="text-sm text-primary hover:underline">{{ __('View All') }}</a>
            @endif
        @endforeach
    </div>

    {{-- Payments panel --}}
    <div x-show="tab === 'payments'">
        @if($recentPayments->isNotEmpty())
            <div class="space-y-3">
                @foreach($recentPayments as $payment)
                    @php($meta = $statusMeta[$payment->status] ?? ['variant' => 'default', 'tint' => 'bg-neutral-100 text-neutral-500'])
                    <div class="flex items-center gap-3 rounded-xl border border-neutral-100 bg-neutral-0 p-3">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {{ $meta['tint'] }}">
                            <i class="ph ph-credit-card"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-neutral-900">
                                {{ $payment->user?->name ?? __('Guest') }}
                            </p>
                            <p class="text-xs text-neutral-400">
                                {{ ucfirst($payment->gateway) }} &middot; {{ $payment->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <div class="flex-1 text-center">
                            <p class="text-sm font-semibold text-neutral-900">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</p>
                        </div>
                        <div class="shrink-0">
                            <x-ui.badge :variant="$meta['variant']">{{ ucfirst($payment->status) }}</x-ui.badge>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-neutral-400">{{ __('No payments recorded.') }}</p>
        @endif
    </div>

    {{-- Users panel --}}
    <div x-show="tab === 'users'" x-cloak>
        @if($recentUsers->isNotEmpty())
            <div class="space-y-3">
                @foreach($recentUsers as $user)
                    <div class="flex items-center gap-3 rounded-xl border border-neutral-100 bg-neutral-0 p-3">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                            <i class="ph ph-user"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-neutral-900">{{ $user->name }}</p>
                            <p class="truncate text-xs text-neutral-400">
                                {{ $user->email }}
                                <span class="text-neutral-300">&middot;</span>
                                {{ __('joined :time', ['time' => $user->created_at->diffForHumans()]) }}
                            </p>
                        </div>
                        <div class="shrink-0">
                            @foreach($user->roles as $role)
                                <x-ui.badge variant="primary">{{ $role->name }}</x-ui.badge>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-neutral-400">{{ __('No users found.') }}</p>
        @endif
    </div>

    {{-- Logins panel --}}
    <div x-show="tab === 'logins'" x-cloak>
        @if($loginActivities->isNotEmpty())
            <div class="space-y-3">
                @foreach($loginActivities as $activity)
                    <div class="flex items-center gap-3 rounded-xl border border-neutral-100 bg-neutral-0 p-3">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-success/10 text-success">
                            <i class="ph ph-activity"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-neutral-900">
                                {{ ucfirst($activity->event) }}
                                @if($activity->user)
                                    — {{ $activity->user->name ?? $activity->user->email ?? __('Unknown') }}
                                @endif
                            </p>
                            <p class="text-xs text-neutral-400">
                                {{ $activity->ip_address }} &middot; {{ $activity->browser }} / {{ $activity->platform }}
                                &middot; {{ $activity->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <div class="shrink-0">
                            <x-ui.badge :variant="$activity->getEventBadgeVariant()">{{ ucfirst($activity->event) }}</x-ui.badge>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-neutral-400">{{ __('No login activity recorded.') }}</p>
        @endif
    </div>
</div>
