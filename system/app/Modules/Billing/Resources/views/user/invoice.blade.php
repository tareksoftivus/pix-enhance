<x-layouts.user :title="__('Invoice')" :search-placeholder="__('Search billing')">
    @php
        $formatMoney = fn (float|int $amount, string $currency = 'USD') => strtoupper($currency).' '.number_format((float) $amount, 2);
        $meta = $invoice->statusMeta();
    @endphp

    <div class="dash__head">
        <div>
            <h1 class="dash__title">{{ __('Invoice') }}</h1>
            <p class="dash__subtitle">{{ $invoice->number }}</p>
        </div>

        <div class="cluster cluster-sm">
            <a class="btn btn-outline btn-sm" href="{{ route('user.billing') }}">
                <i data-lucide="arrow-left"></i>
                {{ __('Billing') }}
            </a>
            <a class="btn btn-primary btn-sm" href="{{ route('user.billing.invoices.download', $invoice) }}">
                <i data-lucide="download"></i>
                {{ __('Download') }}
            </a>
        </div>
    </div>

    <section class="panel" aria-labelledby="invoice-title">
        <div class="panel__head">
            <div>
                <h2 class="panel__title" id="invoice-title">
                    <i data-lucide="receipt-text"></i>
                    {{ $invoice->number }}
                </h2>
                <p class="panel__subtitle">
                    {{ $company['name'] }}
                    @if ($company['email'])
                        &middot; {{ $company['email'] }}
                    @endif
                </p>
            </div>
            <span class="badge badge-sm badge-{{ $meta['variant'] }}">{{ $meta['label'] }}</span>
        </div>

        <div class="panel__body panel__body-flush">
            <div class="table-scroll">
                <table class="data-table">
                    <caption class="sr-only">{{ __('Invoice line items') }}</caption>
                    <thead>
                        <tr>
                            <th scope="col">{{ __('Item') }}</th>
                            <th scope="col">{{ __('Credits') }}</th>
                            <th scope="col" class="data-table__num">{{ __('Amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($line_items as $item)
                            <tr>
                                <td>
                                    <span class="data-table__strong">{{ $item['name'] }}</span>
                                    <span class="setting-row__hint">{{ $item['description'] ?? __('Billing item') }}</span>
                                </td>
                                <td>{{ number_format((int) ($item['credits'] ?? 0)) }}</td>
                                <td class="data-table__num">{{ $formatMoney($item['total'], $invoice->currency) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th scope="row" colspan="2">{{ __('Total') }}</th>
                            <td class="data-table__num data-table__strong">{{ $formatMoney($invoice->total, $invoice->currency) }}</td>
                        </tr>
                        <tr>
                            <th scope="row" colspan="2">{{ __('Paid') }}</th>
                            <td class="data-table__num">{{ $formatMoney($invoice->amount_paid, $invoice->currency) }}</td>
                        </tr>
                        @if ((float) $invoice->amount_refunded > 0)
                            <tr>
                                <th scope="row" colspan="2">{{ __('Refunded') }}</th>
                                <td class="data-table__num">{{ $formatMoney($invoice->amount_refunded, $invoice->currency) }}</td>
                            </tr>
                        @endif
                    </tfoot>
                </table>
            </div>
        </div>
    </section>
</x-layouts.user>
