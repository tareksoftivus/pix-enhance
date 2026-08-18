<x-layouts.admin :title="__('Credits')">
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="heading-4 text-neutral-950">{{ __('Credits') }}</h1>
                <p class="mt-1 text-sm text-neutral-500">{{ __('Inspect wallet balances, audit credit movements and adjust user balances.') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-4">
            <x-ui.kpi-card :title="__('Wallets')" :value="number_format($stats['wallets'])" icon="ph-wallet" color="primary" />
            <x-ui.kpi-card :title="__('Total Balance')" :value="number_format($stats['available'])" icon="ph-coins" color="success" />
            <x-ui.kpi-card :title="__('Reserved')" :value="number_format($stats['reserved'])" icon="ph-lock" color="warning" />
            <x-ui.kpi-card :title="__('Transactions')" :value="number_format($stats['transactions'])" icon="ph-list-bullets" color="primary" />
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_22rem]">
            <div class="section-card">
                <form method="get" action="{{ route('admin.credits.index') }}" class="mb-5 grid grid-cols-1 gap-3 md:grid-cols-[minmax(0,1fr)_14rem_auto]">
                    <x-forms.input
                        :label="__('Search')"
                        name="search"
                        :value="request('search')"
                        :placeholder="__('User, email, reason or metadata')"
                    />

                    <x-forms.input
                        :label="__('Reason')"
                        name="reason"
                        :value="request('reason')"
                        :placeholder="__('pricing_plan_purchase')"
                    />

                    <div class="flex items-end gap-2">
                        <x-ui.button type="submit">
                            <i class="ph ph-magnifying-glass"></i> {{ __('Filter') }}
                        </x-ui.button>
                        @if (request()->hasAny(['search', 'reason']))
                            <x-ui.button variant="outline" href="{{ route('admin.credits.index') }}">
                                {{ __('Clear') }}
                            </x-ui.button>
                        @endif
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-neutral-100 text-xs font-semibold uppercase tracking-wide text-neutral-500">
                                <th class="py-3 pr-4">{{ __('User') }}</th>
                                <th class="py-3 pr-4">{{ __('Reason') }}</th>
                                <th class="py-3 pr-4">{{ __('Amount') }}</th>
                                <th class="py-3 pr-4">{{ __('Balance') }}</th>
                                <th class="py-3 pr-4">{{ __('Date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions as $transaction)
                                @php $metadata = $transaction->metadata ?? []; @endphp
                                <tr class="border-b border-neutral-100 last:border-0">
                                    <td class="py-3 pr-4">
                                        <p class="text-sm font-semibold text-neutral-950">{{ $transaction->user?->name ?? __('Deleted user') }}</p>
                                        <p class="text-xs text-neutral-500">{{ $transaction->user?->email ?? __('No email') }}</p>
                                    </td>
                                    <td class="py-3 pr-4">
                                        <p class="text-sm font-medium text-neutral-800">{{ \Illuminate\Support\Str::headline($transaction->reason) }}</p>
                                        <p class="text-xs text-neutral-500">{{ $metadata['pricing_plan_name'] ?? $metadata['credit_pack_name'] ?? $metadata['note'] ?? __('Ledger entry') }}</p>
                                    </td>
                                    <td class="py-3 pr-4">
                                        <x-ui.badge :variant="$transaction->amount > 0 ? 'success' : 'warning'">
                                            {{ $transaction->amount > 0 ? '+' : '' }}{{ number_format($transaction->amount) }}
                                        </x-ui.badge>
                                    </td>
                                    <td class="py-3 pr-4 text-sm font-semibold text-neutral-950">{{ number_format($transaction->balance_after) }}</td>
                                    <td class="py-3 pr-4 text-sm text-neutral-500">{{ $transaction->created_at?->format('M j, Y g:i A') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-sm text-neutral-400">{{ __('No credit transactions found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-5">
                    <x-tables.pagination :paginator="$transactions" />
                </div>
            </div>

            <div class="section-card">
                <h2 class="text-base font-bold text-neutral-950">{{ __('Adjust Balance') }}</h2>
                <p class="mt-1 text-sm text-neutral-500">{{ __('Use positive numbers to add credits and negative numbers to remove credits.') }}</p>

                <form method="post" action="{{ route('admin.credits.adjustments.store') }}" class="mt-5 space-y-4">
                    @csrf

                    <x-forms.input
                        :label="__('User ID')"
                        name="user_id"
                        type="number"
                        min="1"
                        :value="old('user_id')"
                        required
                    />

                    <x-forms.input
                        :label="__('Amount')"
                        name="amount"
                        type="number"
                        :value="old('amount')"
                        :placeholder="__('100 or -50')"
                        required
                    />

                    <x-forms.textarea
                        :label="__('Note')"
                        name="note"
                        :value="old('note')"
                        :placeholder="__('Reason for this adjustment')"
                    />

                    <x-ui.button type="submit" class="w-full">
                        <i class="ph ph-check"></i> {{ __('Save Adjustment') }}
                    </x-ui.button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.admin>
