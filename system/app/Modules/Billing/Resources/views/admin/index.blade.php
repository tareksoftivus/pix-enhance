<x-layouts.admin :title="__('Billing')">
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="heading-4 text-neutral-950">{{ __('Billing') }}</h1>
                <p class="mt-1 text-sm text-neutral-500">{{ __('Review customer invoices, payment totals and pending billing activity.') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-4">
            <x-ui.kpi-card :title="__('Paid Invoices')" :value="number_format($summary['paid_invoices'])" icon="ph-receipt" color="success" />
            <x-ui.kpi-card :title="__('Open Invoices')" :value="number_format($summary['open_invoices'])" icon="ph-clock" color="warning" />
            <x-ui.kpi-card :title="__('Gross Revenue')" :value="strtoupper(config('billing.currency', 'USD')).' '.number_format($summary['gross_revenue'], 2)" icon="ph-currency-dollar" color="primary" />
            <x-ui.kpi-card :title="__('Refunded')" :value="strtoupper(config('billing.currency', 'USD')).' '.number_format($summary['refunded_revenue'], 2)" icon="ph-arrow-counter-clockwise" color="warning" />
        </div>

        <div class="section-card">
            <form method="get" action="{{ route('admin.billing.index') }}" class="mb-5 grid grid-cols-1 gap-3 md:grid-cols-[minmax(0,1fr)_12rem_auto]">
                <x-forms.input
                    :label="__('Search')"
                    name="search"
                    :value="$filters['search'] ?? ''"
                    :placeholder="__('Invoice, customer or metadata')"
                />

                <x-forms.input
                    :label="__('Status')"
                    name="status"
                    :value="$filters['status'] ?? ''"
                    :placeholder="__('paid')"
                />

                <div class="flex items-end gap-2">
                    <x-ui.button type="submit">
                        <i class="ph ph-magnifying-glass"></i> {{ __('Filter') }}
                    </x-ui.button>
                    @if (request()->hasAny(['search', 'status']))
                        <x-ui.button variant="outline" href="{{ route('admin.billing.index') }}">
                            {{ __('Clear') }}
                        </x-ui.button>
                    @endif
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-neutral-100 text-xs font-semibold uppercase tracking-wide text-neutral-500">
                            <th class="py-3 pr-4">{{ __('Invoice') }}</th>
                            <th class="py-3 pr-4">{{ __('Customer') }}</th>
                            <th class="py-3 pr-4">{{ __('Total') }}</th>
                            <th class="py-3 pr-4">{{ __('Status') }}</th>
                            <th class="py-3 pr-4">{{ __('Issued') }}</th>
                            <th class="py-3 pr-4 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($invoices as $invoice)
                            @php $meta = $invoice->statusMeta(); @endphp
                            <tr class="border-b border-neutral-100 last:border-0">
                                <td class="py-3 pr-4">
                                    <p class="text-sm font-semibold text-neutral-950">{{ $invoice->number }}</p>
                                    <p class="text-xs text-neutral-500">{{ $invoice->payment?->uuid ?? __('No payment') }}</p>
                                </td>
                                <td class="py-3 pr-4">
                                    <p class="text-sm font-semibold text-neutral-950">{{ $invoice->billable?->name ?? __('Deleted user') }}</p>
                                    <p class="text-xs text-neutral-500">{{ $invoice->billable?->email ?? __('No email') }}</p>
                                </td>
                                <td class="py-3 pr-4 text-sm font-semibold text-neutral-950">{{ strtoupper($invoice->currency) }} {{ number_format($invoice->total, 2) }}</td>
                                <td class="py-3 pr-4">
                                    <x-ui.badge :variant="$meta['variant']">{{ $meta['label'] }}</x-ui.badge>
                                </td>
                                <td class="py-3 pr-4 text-sm text-neutral-500">{{ $invoice->issued_at?->format('M j, Y g:i A') ?? $invoice->created_at?->format('M j, Y g:i A') }}</td>
                                <td class="py-3 pr-4 text-right">
                                    <x-ui.button variant="outline" size="sm" href="{{ route('admin.billing.invoices.show', $invoice) }}">
                                        <i class="ph ph-eye"></i> {{ __('View') }}
                                    </x-ui.button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-sm text-neutral-400">{{ __('No invoices found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-5">
                <x-tables.pagination :paginator="$invoices" />
            </div>
        </div>
    </div>
</x-layouts.admin>
