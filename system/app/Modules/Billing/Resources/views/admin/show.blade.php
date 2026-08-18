<x-layouts.admin :title="__('Invoice Detail')">
    @php $meta = $invoice->statusMeta(); @endphp

    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="heading-4 text-neutral-950">{{ __('Invoice') }} <span class="text-neutral-400">{{ $invoice->number }}</span></h1>
                <p class="mt-1 text-sm text-neutral-500">{{ $invoice->payment?->description ?: __('Billing invoice') }}</p>
            </div>
            <x-ui.button variant="outline" href="{{ route('admin.billing.index') }}">
                <i class="ph ph-arrow-left"></i> {{ __('Back') }}
            </x-ui.button>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_22rem]">
            <div class="section-card space-y-5">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-bold text-neutral-950">{{ __('Invoice Summary') }}</h2>
                        <p class="text-sm text-neutral-500">{{ __('Issued :date', ['date' => $invoice->issued_at?->format('M j, Y g:i A') ?? $invoice->created_at?->format('M j, Y g:i A')]) }}</p>
                    </div>
                    <x-ui.badge :variant="$meta['variant']">{{ $meta['label'] }}</x-ui.badge>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-neutral-100 text-xs font-semibold uppercase tracking-wide text-neutral-500">
                                <th class="py-3 pr-4">{{ __('Item') }}</th>
                                <th class="py-3 pr-4">{{ __('Credits') }}</th>
                                <th class="py-3 pr-4 text-right">{{ __('Total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($line_items as $item)
                                <tr class="border-b border-neutral-100 last:border-0">
                                    <td class="py-3 pr-4">
                                        <p class="text-sm font-semibold text-neutral-950">{{ $item['name'] }}</p>
                                        <p class="text-xs text-neutral-500">{{ $item['description'] ?? __('Billing item') }}</p>
                                    </td>
                                    <td class="py-3 pr-4 text-sm text-neutral-600">{{ number_format((int) ($item['credits'] ?? 0)) }}</td>
                                    <td class="py-3 pr-4 text-right text-sm font-semibold text-neutral-950">{{ strtoupper($invoice->currency) }} {{ number_format((float) $item['total'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="grid grid-cols-1 gap-4 border-t border-neutral-100 pt-4 sm:grid-cols-3">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-neutral-400">{{ __('Total') }}</p>
                        <p class="mt-1 text-lg font-bold text-neutral-950">{{ strtoupper($invoice->currency) }} {{ number_format($invoice->total, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-neutral-400">{{ __('Paid') }}</p>
                        <p class="mt-1 text-lg font-bold text-neutral-950">{{ strtoupper($invoice->currency) }} {{ number_format($invoice->amount_paid, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-neutral-400">{{ __('Refunded') }}</p>
                        <p class="mt-1 text-lg font-bold text-neutral-950">{{ strtoupper($invoice->currency) }} {{ number_format($invoice->amount_refunded, 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="section-card space-y-4">
                    <h2 class="text-base font-bold text-neutral-950">{{ __('Customer') }}</h2>
                    <div>
                        <p class="text-sm font-semibold text-neutral-950">{{ $customer?->name ?? __('Deleted user') }}</p>
                        <p class="text-sm text-neutral-500">{{ $customer?->email ?? __('No email') }}</p>
                    </div>
                </div>

                <div class="section-card space-y-4">
                    <h2 class="text-base font-bold text-neutral-950">{{ __('Payment') }}</h2>
                    <div>
                        <p class="text-sm font-semibold text-neutral-950">{{ $payment?->uuid ?? __('No payment') }}</p>
                        <p class="text-sm text-neutral-500">
                            @if ($payment)
                                {{ \Illuminate\Support\Str::headline($payment->gateway) }} &middot; {{ \Illuminate\Support\Str::headline($payment->status) }}
                            @else
                                {{ __('Detached invoice') }}
                            @endif
                        </p>
                    </div>
                    @if ($payment && \Illuminate\Support\Facades\Route::has('admin.payments.show'))
                        <x-ui.button variant="outline" href="{{ route('admin.payments.show', $payment) }}">
                            <i class="ph ph-credit-card"></i> {{ __('Open Payment') }}
                        </x-ui.button>
                    @endif
                </div>

                <div class="section-card space-y-3">
                    <h2 class="text-base font-bold text-neutral-950">{{ __('Actions') }}</h2>
                    @if (! $invoice->isPaid() && $invoice->status !== \App\Modules\Billing\Models\BillingInvoice::STATUS_VOID)
                        <form method="post" action="{{ route('admin.billing.invoices.mark-paid', $invoice) }}">
                            @csrf
                            <x-ui.button type="submit" class="w-full">
                                <i class="ph ph-check-circle"></i> {{ __('Mark Paid') }}
                            </x-ui.button>
                        </form>
                    @endif
                    @if (! $invoice->isPaid() && $invoice->status !== \App\Modules\Billing\Models\BillingInvoice::STATUS_VOID)
                        <form method="post" action="{{ route('admin.billing.invoices.void', $invoice) }}">
                            @csrf
                            <x-ui.button type="submit" variant="outline" class="w-full">
                                <i class="ph ph-x-circle"></i> {{ __('Void Invoice') }}
                            </x-ui.button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
