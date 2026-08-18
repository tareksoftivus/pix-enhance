<x-layouts.user :title="__('Payment')" :search-placeholder="__('Search billing')">
    @php
        $formatMoney = fn (float|int $amount, string $currency = 'USD') => strtoupper($currency).' '.number_format((float) $amount, 2);
        $invoiceMeta = $invoice->statusMeta();
    @endphp

    <div class="dash__head">
        <div>
            <h1 class="dash__title">{{ __('Payment') }}</h1>
            <p class="dash__subtitle">{{ $payment->description ?: __('Checkout record') }}</p>
        </div>

        <div class="cluster cluster-sm">
            <a class="btn btn-outline btn-sm" href="{{ route('user.billing') }}">
                <i data-lucide="arrow-left"></i>
                {{ __('Billing') }}
            </a>
            <a class="btn btn-primary btn-sm" href="{{ route('user.billing.invoices.download', $invoice) }}">
                <i data-lucide="download"></i>
                {{ __('Receipt') }}
            </a>
        </div>
    </div>

    <section class="plan-summary" aria-labelledby="payment-title">
        <div>
            <h2 class="plan-summary__name" id="payment-title">
                <i data-lucide="credit-card"></i>
                {{ $formatMoney($payment->amount, $payment->currency) }}
                <span class="badge badge-sm {{ $payment->status === 'completed' ? 'badge-success' : ($payment->status === 'failed' ? 'badge-danger' : 'badge-warning') }}">
                    {{ \Illuminate\Support\Str::headline($payment->status) }}
                </span>
            </h2>
            <p class="plan-summary__price">
                {{ \Illuminate\Support\Str::headline($payment->gateway) }}
                @if ($payment->payment_method)
                    &middot; {{ \Illuminate\Support\Str::headline($payment->payment_method) }}
                @endif
            </p>
        </div>

        <div>
            <div class="plan-summary__meter-head">
                <span class="plan-summary__meter-label">{{ __('Invoice') }}</span>
                <span class="plan-summary__meter-value">{{ $invoice->number }}</span>
            </div>
            <div class="progress" data-progress="{{ $invoice->isPaid() ? 100 : 35 }}">
                <div class="progress__bar"></div>
            </div>
            <p class="plan-summary__meter-note">
                <span class="badge badge-sm badge-{{ $invoiceMeta['variant'] }}">{{ $invoiceMeta['label'] }}</span>
            </p>
        </div>
    </section>

    <section class="panel mt-lg" aria-labelledby="details-title">
        <div class="panel__head">
            <h2 class="panel__title" id="details-title">
                <i data-lucide="list-checks"></i>
                {{ __('Details') }}
            </h2>
        </div>

        <div class="panel__body panel__body-flush">
            <div class="table-scroll">
                <table class="data-table">
                    <caption class="sr-only">{{ __('Payment details') }}</caption>
                    <tbody>
                        <tr>
                            <th scope="row">{{ __('Payment ID') }}</th>
                            <td class="data-table__num">{{ $payment->uuid }}</td>
                        </tr>
                        <tr>
                            <th scope="row">{{ __('Gateway ID') }}</th>
                            <td class="data-table__num">{{ $payment->gateway_payment_id ?: __('Pending') }}</td>
                        </tr>
                        <tr>
                            <th scope="row">{{ __('Created') }}</th>
                            <td class="data-table__num">{{ $payment->created_at?->format('M j, Y g:i A') }}</td>
                        </tr>
                        <tr>
                            <th scope="row">{{ __('Paid') }}</th>
                            <td class="data-table__num">{{ $payment->paid_at?->format('M j, Y g:i A') ?? __('Not paid yet') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</x-layouts.user>
