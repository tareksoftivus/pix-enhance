<x-layouts.user :title="__('Billing')" :search-placeholder="__('Search payments')">
    @php
        $billing = $billing ?? [];
        $summary = $billing['credit_summary'] ?? $creditSummary ?? [
            'balance' => 0,
            'reserved' => 0,
            'available' => 0,
            'lifetime_earned' => 0,
            'lifetime_spent' => 0,
            'lifetime_refunded' => 0,
        ];
        $billingSummary = $billing['summary'] ?? [];
        $creditPacks = $billing['credit_packs'] ?? $creditPacks ?? [];
        $pricingPlans = $billing['pricing_plans'] ?? $pricingPlans ?? collect();
        $creditTransactions = $billing['credit_transactions'] ?? $creditTransactions ?? null;
        $payments = $billing['payments'] ?? $payments ?? null;
        $invoices = $billing['invoices'] ?? null;
        $orders = $billing['orders'] ?? null;
        $transactions = $creditTransactions ? collect($creditTransactions->items()) : collect();
        $paymentRows = $payments ? collect($payments->items()) : collect($payments ?? []);
        $invoiceRows = $invoices ? collect($invoices->items()) : collect();
        $orderRows = $orders ? collect($orders->items()) : collect();
        $currentPlan = $billing['current_plan'] ?? null;
        $currentPlanName = $currentPlan['name'] ?? null;
        $formatMoney = fn (float|int $amount, string $currency = 'USD') => strtoupper($currency).' '.number_format((float) $amount, 2);
    @endphp

    <div class="dash__head">
        <div>
            <h1 class="dash__title">{{ __('Billing') }}</h1>
            <p class="dash__subtitle">
                {{ __('Manage credits, start a plan, buy top-ups and review recent payment activity.') }}
            </p>
        </div>

        <div class="cluster cluster-sm">
            <a class="btn btn-outline btn-sm" href="{{ \Illuminate\Support\Facades\Route::has('user.support-tickets.create') ? route('user.support-tickets.create') : '#' }}">
                <i data-lucide="life-buoy"></i>
                {{ __('Billing support') }}
            </a>
        </div>
    </div>

    <section class="plan-summary" aria-labelledby="credits-title">
        <div>
            <h2 class="plan-summary__name" id="credits-title">
                <i data-lucide="coins"></i>
                {{ __('Credit wallet') }}
                <span class="badge badge-sm {{ ($summary['available'] ?? 0) > 0 ? 'badge-success' : 'badge-warning' }}">
                    {{ ($summary['available'] ?? 0) > 0 ? __('Ready') : __('Empty') }}
                </span>
            </h2>

            <p class="plan-summary__price">
                <strong>{{ number_format($summary['available'] ?? 0) }}</strong> {{ __('credits available') }}
                @if (($summary['reserved'] ?? 0) > 0)
                    &middot; {{ __(':count reserved', ['count' => number_format($summary['reserved'])]) }}
                @endif
            </p>

            <div class="plan-summary__actions">
                <a class="btn btn-primary btn-sm" href="#packs" data-ripple>
                    <i data-lucide="zap"></i>
                    {{ __('Buy credits') }}
                </a>
                <a class="btn btn-outline btn-sm" href="#change-plan">
                    <i data-lucide="rocket"></i>
                    {{ __('Choose plan') }}
                </a>
            </div>
        </div>

        <div>
            <div class="plan-summary__meter-head">
                <span class="plan-summary__meter-label">{{ __('Lifetime usage') }}</span>
                <span class="plan-summary__meter-value">
                    {{ number_format($summary['lifetime_spent'] ?? 0) }} / {{ number_format($summary['lifetime_earned'] ?? 0) }}
                </span>
            </div>

            <div class="progress" data-progress="{{ ($summary['lifetime_earned'] ?? 0) > 0 ? min(100, (int) round((($summary['lifetime_spent'] ?? 0) / $summary['lifetime_earned']) * 100)) : 0 }}">
                <div class="progress__bar"></div>
            </div>

            <p class="plan-summary__meter-note">
                @if ($currentPlanName)
                    {{ __('Current plan source: :plan', ['plan' => $currentPlanName]) }}
                @else
                    {{ __('Plan credits will appear here after your first checkout.') }}
                @endif
            </p>
        </div>
    </section>

    <section class="stats-grid mt-lg" aria-label="{{ __('Billing overview') }}">
        <article class="stat-card">
            <span class="stat-card__icon"><i data-lucide="receipt"></i></span>
            <span class="stat-card__label">{{ __('Paid invoices') }}</span>
            <strong class="stat-card__value">{{ number_format($billingSummary['paid_invoices'] ?? 0) }}</strong>
        </article>
        <article class="stat-card">
            <span class="stat-card__icon"><i data-lucide="clock"></i></span>
            <span class="stat-card__label">{{ __('Open invoices') }}</span>
            <strong class="stat-card__value">{{ number_format($billingSummary['open_invoices'] ?? 0) }}</strong>
        </article>
        <article class="stat-card">
            <span class="stat-card__icon"><i data-lucide="badge-dollar-sign"></i></span>
            <span class="stat-card__label">{{ __('Lifetime paid') }}</span>
            <strong class="stat-card__value">{{ $formatMoney($billingSummary['gross_revenue'] ?? 0) }}</strong>
        </article>
    </section>

    <section class="panel mt-lg" id="packs" aria-labelledby="packs-title">
        <div class="panel__head">
            <h2 class="panel__title" id="packs-title">
                <i data-lucide="zap"></i>
                {{ __('Top up credits') }}
            </h2>
            <p class="panel__subtitle">
                {{ __('One-off packs are added to your wallet after payment confirmation.') }}
            </p>
        </div>

        <div class="panel__body">
            <div class="pack-grid">
                @foreach ($creditPacks as $pack)
                    <div class="pack">
                        <span class="pack__credits">{{ number_format($pack['credits']) }}</span>
                        <span class="pack__price">{{ $formatMoney($pack['price'], $pack['currency']) }}</span>
                        <span class="pack__rate">{{ $formatMoney($pack['rate'], $pack['currency']) }} / {{ __('credit') }}</span>
                        @if ($pack['badge'])
                            <span class="badge badge-sm {{ $pack['slug'] === 'enterprise' ? 'badge-success' : 'badge-primary' }} pack__tag">{{ __($pack['badge']) }}</span>
                        @endif
                        <a href="{{ route('user.checkout', ['type' => 'pack', 'pack' => $pack['slug']]) }}" class="btn btn-primary btn-sm btn-block mt-md" data-ripple>
                            <i data-lucide="credit-card"></i>
                            {{ __('Buy pack') }}
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mt-xl" id="change-plan" aria-labelledby="change-plan-title">
        <div class="dash__section-head">
            <h2 class="dash__section-title" id="change-plan-title">
                <i data-lucide="rocket"></i>
                {{ __('Plans') }}
            </h2>
            <a class="btn-link btn-link-sm" href="{{ \Illuminate\Support\Facades\Route::has('home') ? route('home') : '/' }}#pricing">
                {{ __('Compare every feature') }}
                <i data-lucide="arrow-right"></i>
            </a>
        </div>

        <div class="pricing-grid pricing-grid-3">
            @forelse ($pricingPlans as $plan)
                <article class="plan {{ $plan->is_featured ? 'plan-featured' : '' }}">
                    @if ($plan->is_featured)
                        <span class="plan__ribbon">{{ __('Popular') }}</span>
                    @endif

                    <div class="plan__head">
                        <h3 class="plan__name">{{ $plan->name }}</h3>
                        <p class="plan__tagline">{{ $plan->tagline }}</p>
                    </div>
                    <p class="plan__price">
                        <span class="plan__currency">$</span>
                        <span class="plan__amount">{{ number_format($plan->price_monthly) }}</span>
                        <span class="plan__period">/{{ __('month') }}</span>
                    </p>
                    <ul class="plan__features">
                        <li class="plan__feature">
                            <span class="plan__feature-check"><i data-lucide="check"></i></span>
                            <strong>{{ number_format($plan->credits_monthly) }}</strong> {{ __('credits a month') }}
                        </li>
                        @foreach (array_slice($plan->features ?? [], 0, 3) as $feature)
                            <li class="plan__feature"><span class="plan__feature-check"><i data-lucide="check"></i></span>{{ $feature }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ route('user.checkout', ['type' => 'plan', 'plan' => $plan->id]) }}" class="btn {{ $plan->is_featured ? 'btn-primary' : 'btn-outline' }} btn-block plan__cta" data-ripple>
                        {{ $plan->cta_label ?: __('Choose plan') }}
                    </a>
                </article>
            @empty
                <div class="empty-state">
                    <span class="empty-state__icon" aria-hidden="true"><i data-lucide="rocket"></i></span>
                    <h3>{{ __('No plans available') }}</h3>
                    <p>{{ __('Active pricing plans will appear here once they are published.') }}</p>
                </div>
            @endforelse
        </div>
    </section>

    <section class="panel mt-xl" aria-labelledby="ledger-title">
        <div class="panel__head">
            <h2 class="panel__title" id="ledger-title">
                <i data-lucide="activity"></i>
                {{ __('Credit ledger') }}
            </h2>
            <p class="panel__subtitle">{{ __('Every credit added, spent or adjusted on your account.') }}</p>
        </div>

        <div class="panel__body panel__body-flush">
            <div class="table-scroll">
                <table class="data-table">
                    <caption class="sr-only">{{ __('Credit ledger') }}</caption>
                    <thead>
                        <tr>
                            <th scope="col">{{ __('Date') }}</th>
                            <th scope="col">{{ __('Activity') }}</th>
                            <th scope="col">{{ __('Amount') }}</th>
                            <th scope="col">{{ __('Balance') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $transaction)
                            @php $metadata = $transaction->metadata ?? []; @endphp
                            <tr>
                                <td class="data-table__num">{{ $transaction->created_at?->format('M j, Y') }}</td>
                                <td>
                                    <span class="data-table__strong">{{ \Illuminate\Support\Str::headline($transaction->reason) }}</span>
                                    <span class="setting-row__hint">{{ $metadata['credit_pack_name'] ?? $metadata['pricing_plan_name'] ?? $metadata['note'] ?? __('Credit wallet activity') }}</span>
                                </td>
                                <td class="data-table__num">
                                    <span class="badge badge-sm {{ $transaction->amount > 0 ? 'badge-success' : 'badge-warning' }}">
                                        {{ $transaction->amount > 0 ? '+' : '' }}{{ number_format($transaction->amount) }}
                                    </span>
                                </td>
                                <td class="data-table__num data-table__strong">{{ number_format($transaction->balance_after) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="empty-state">
                                        <span class="empty-state__icon" aria-hidden="true"><i data-lucide="coins"></i></span>
                                        <h3>{{ __('No credit activity yet') }}</h3>
                                        <p>{{ __('Signup credits, purchases and render spending will appear here.') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="panel mt-xl" aria-labelledby="orders-title">
        <div class="panel__head">
            <h2 class="panel__title" id="orders-title">
                <i data-lucide="shopping-cart"></i>
                {{ __('Orders') }}
            </h2>
            <p class="panel__subtitle">{{ __('Every credit pack or plan checkout you have started.') }}</p>
        </div>

        <div class="panel__body panel__body-flush">
            <div class="table-scroll">
                <table class="data-table">
                    <caption class="sr-only">{{ __('Orders') }}</caption>
                    <thead>
                        <tr>
                            <th scope="col">{{ __('Order') }}</th>
                            <th scope="col">{{ __('Date') }}</th>
                            <th scope="col">{{ __('Gateway') }}</th>
                            <th scope="col">{{ __('Total') }}</th>
                            <th scope="col">{{ __('Status') }}</th>
                            <th scope="col" class="data-table__num">{{ __('Invoice') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orderRows as $order)
                            @php $orderMeta = $order->statusMeta(); @endphp
                            <tr>
                                <td>
                                    <span class="data-table__strong">{{ $order->name }}</span>
                                    <span class="setting-row__hint">{{ number_format($order->credits) }} {{ __('credits') }} · {{ \Illuminate\Support\Str::headline($order->type) }}</span>
                                </td>
                                <td class="data-table__num">{{ $order->created_at?->format('M j, Y') }}</td>
                                <td>{{ $order->gateway ? \Illuminate\Support\Str::headline($order->gateway) : __('—') }}</td>
                                <td class="data-table__num">{{ $formatMoney($order->total, $order->currency) }}</td>
                                <td>
                                    <span class="badge badge-sm badge-{{ $orderMeta['variant'] }}">{{ $orderMeta['label'] }}</span>
                                </td>
                                <td class="data-table__num">
                                    @if ($order->invoice)
                                        <a class="btn btn-outline btn-sm" href="{{ route('user.billing.invoices.show', $order->invoice) }}">
                                            <i data-lucide="receipt-text"></i>
                                            {{ __('View') }}
                                        </a>
                                    @else
                                        <span class="setting-row__hint">{{ __('Not yet available') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <span class="empty-state__icon" aria-hidden="true"><i data-lucide="shopping-cart"></i></span>
                                        <h3>{{ __('No orders yet') }}</h3>
                                        <p>{{ __('Credit pack and plan checkouts will appear here.') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="panel" aria-labelledby="invoices-title">
        <div class="panel__head">
            <h2 class="panel__title" id="invoices-title">
                <i data-lucide="receipt-text"></i>
                {{ __('Invoices') }}
            </h2>
            <p class="panel__subtitle">{{ __('Receipts generated from completed and pending checkout activity.') }}</p>
        </div>

        <div class="panel__body panel__body-flush">
            <div class="table-scroll">
                <table class="data-table">
                    <caption class="sr-only">{{ __('Invoices') }}</caption>
                    <thead>
                        <tr>
                            <th scope="col">{{ __('Invoice') }}</th>
                            <th scope="col">{{ __('Issued') }}</th>
                            <th scope="col">{{ __('Total') }}</th>
                            <th scope="col">{{ __('Status') }}</th>
                            <th scope="col" class="data-table__num">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($invoiceRows as $invoice)
                            @php $meta = $invoice->statusMeta(); @endphp
                            <tr>
                                <td>
                                    <a class="data-table__strong" href="{{ route('user.billing.invoices.show', $invoice) }}">{{ $invoice->number }}</a>
                                    <span class="setting-row__hint">{{ $invoice->payment?->description ?: __('Billing invoice') }}</span>
                                </td>
                                <td class="data-table__num">{{ $invoice->issued_at?->format('M j, Y') ?? $invoice->created_at?->format('M j, Y') }}</td>
                                <td class="data-table__num">{{ $formatMoney($invoice->total, $invoice->currency) }}</td>
                                <td>
                                    <span class="badge badge-sm badge-{{ $meta['variant'] }}">{{ $meta['label'] }}</span>
                                </td>
                                <td class="data-table__num">
                                    <a class="btn btn-outline btn-sm" href="{{ route('user.billing.invoices.download', $invoice) }}">
                                        <i data-lucide="download"></i>
                                        {{ __('Download') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <span class="empty-state__icon" aria-hidden="true"><i data-lucide="receipt-text"></i></span>
                                        <h3>{{ __('No invoices yet') }}</h3>
                                        <p>{{ __('Your invoices will appear here after checkout starts.') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="panel" aria-labelledby="payments-title">
        <div class="panel__head">
            <h2 class="panel__title" id="payments-title">
                <i data-lucide="credit-card"></i>
                {{ __('Payments') }}
            </h2>
            <p class="panel__subtitle">{{ __('Recent checkout records from the active payment gateway.') }}</p>
        </div>

        <div class="panel__body panel__body-flush">
            <div class="table-scroll">
                <table class="data-table">
                    <caption class="sr-only">{{ __('Payment history') }}</caption>
                    <thead>
                        <tr>
                            <th scope="col">{{ __('Date') }}</th>
                            <th scope="col">{{ __('Description') }}</th>
                            <th scope="col">{{ __('Gateway') }}</th>
                            <th scope="col">{{ __('Amount') }}</th>
                            <th scope="col">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($paymentRows as $payment)
                            <tr>
                                <td class="data-table__num">{{ $payment->created_at?->format('M j, Y') }}</td>
                                <td>
                                    <a class="data-table__strong" href="{{ route('user.billing.payments.show', $payment->uuid) }}">{{ $payment->description ?: __('Payment') }}</a>
                                    <span class="setting-row__hint">{{ $payment->uuid }}</span>
                                </td>
                                <td>{{ \Illuminate\Support\Str::headline($payment->gateway) }}</td>
                                <td class="data-table__num">{{ $formatMoney($payment->amount, $payment->currency) }}</td>
                                <td>
                                    <span class="badge badge-sm {{ $payment->status === 'completed' ? 'badge-success' : ($payment->status === 'failed' ? 'badge-danger' : 'badge-warning') }}">
                                        {{ \Illuminate\Support\Str::headline($payment->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <span class="empty-state__icon" aria-hidden="true"><i data-lucide="credit-card"></i></span>
                                        <h3>{{ __('No payments yet') }}</h3>
                                        <p>{{ __('Credit pack and plan payments will appear here after checkout.') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</x-layouts.user>
