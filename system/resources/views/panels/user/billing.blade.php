<x-layouts.user :title="__('Billing')" :search-placeholder="__('Search invoices')">
    <div class="dash__head">
        <div>
            <h1 class="dash__title">{{ __('Billing') }}</h1>
            <p class="dash__subtitle">
                {{ __("Your plan, credits, payment method and every invoice we've issued.") }}
            </p>
        </div>

        <div class="cluster cluster-sm">
            <a class="btn btn-outline btn-sm" href="#">
                <i data-lucide="life-buoy"></i>
                {{ __('Billing support') }}
            </a>
        </div>
    </div>

    <section class="plan-summary" aria-labelledby="plan-title">
        <div>
            <h2 class="plan-summary__name" id="plan-title">
                <i data-lucide="gem"></i>
                {{ __('Studio') }}
                <span class="badge badge-sm badge-success">{{ __('Active') }}</span>
            </h2>

            <p class="plan-summary__price">
                <strong>$49</strong> {{ __('per month') }} &middot; {{ __('renews 1 September 2026') }}
            </p>

            <div class="plan-summary__actions">
                <a class="btn btn-primary btn-sm" href="#change-plan" data-ripple>
                    <i data-lucide="rocket"></i>
                    {{ __('Change plan') }}
                </a>
                <a class="btn btn-outline btn-sm" href="#packs">
                    <i data-lucide="zap"></i>
                    {{ __('Buy credits') }}
                </a>
                <button type="button" class="btn btn-ghost btn-sm">
                    {{ __('Cancel subscription') }}
                </button>
            </div>
        </div>

        <div>
            <div class="plan-summary__meter-head">
                <span class="plan-summary__meter-label">{{ __('Credits used this cycle') }}</span>
                <span class="plan-summary__meter-value">316 / 500</span>
            </div>

            <div class="progress" data-progress="63">
                <div class="progress__bar"></div>
            </div>

            <p class="plan-summary__meter-note">
                {{ __('184 credits left') }} &middot; {{ __('resets in 27 days. Unused credits do not roll over.') }}
            </p>
        </div>
    </section>

    <section class="panel mt-lg" id="packs" aria-labelledby="packs-title">
        <div class="panel__head">
            <h2 class="panel__title" id="packs-title">
                <i data-lucide="coins"></i>
                {{ __('Top up credits') }}
            </h2>
            <p class="panel__subtitle">
                {{ __('One-off packs. They never expire and are spent only after your monthly allowance.') }}
            </p>
        </div>

        <div class="panel__body">
            <div class="pack-grid">
                @foreach ([
                    ['credits' => '100', 'price' => '$12', 'rate' => '$0.120 / credit'],
                    ['credits' => '500', 'price' => '$52', 'rate' => '$0.104 / credit', 'tag' => __('Popular'), 'variant' => 'badge-primary'],
                    ['credits' => '2,000', 'price' => '$180', 'rate' => '$0.090 / credit'],
                    ['credits' => '10,000', 'price' => '$780', 'rate' => '$0.078 / credit', 'tag' => __('Best rate'), 'variant' => 'badge-success'],
                ] as $pack)
                    <button type="button" class="pack">
                        <span class="pack__credits">{{ $pack['credits'] }}</span>
                        <span class="pack__price">{{ $pack['price'] }}</span>
                        <span class="pack__rate">{{ $pack['rate'] }}</span>
                        @isset($pack['tag'])
                            <span class="badge badge-sm {{ $pack['variant'] }} pack__tag">{{ $pack['tag'] }}</span>
                        @endisset
                    </button>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mt-xl" id="change-plan" aria-labelledby="change-plan-title">
        <div class="dash__section-head">
            <h2 class="dash__section-title" id="change-plan-title">
                <i data-lucide="rocket"></i>
                {{ __('Change plan') }}
            </h2>
            <a class="btn-link btn-link-sm" href="{{ \Illuminate\Support\Facades\Route::has('home') ? route('home') : '/' }}#pricing">
                {{ __('Compare every feature') }}
                <i data-lucide="arrow-right"></i>
            </a>
        </div>

        <div class="pricing-grid pricing-grid-3">
            <article class="plan">
                <div class="plan__head">
                    <h3 class="plan__name">{{ __('Starter') }}</h3>
                    <p class="plan__tagline">{{ __('For occasional touch-ups.') }}</p>
                </div>
                <p class="plan__price">
                    <span class="plan__currency">$</span>
                    <span class="plan__amount">12</span>
                    <span class="plan__period">/{{ __('month') }}</span>
                </p>
                <ul class="plan__features">
                    <li class="plan__feature"><span class="plan__feature-check"><i data-lucide="check"></i></span>{{ __('100 credits a month') }}</li>
                    <li class="plan__feature"><span class="plan__feature-check"><i data-lucide="check"></i></span>{{ __('Up to 4× upscaling') }}</li>
                    <li class="plan__feature"><span class="plan__feature-check"><i data-lucide="check"></i></span>{{ __('7-day image history') }}</li>
                </ul>
                <button type="button" class="btn btn-outline btn-block plan__cta">{{ __('Downgrade') }}</button>
            </article>

            <article class="plan plan-featured">
                <span class="plan__ribbon">{{ __('Current plan') }}</span>
                <div class="plan__head">
                    <h3 class="plan__name">{{ __('Studio') }}</h3>
                    <p class="plan__tagline">{{ __('For working photographers and teams.') }}</p>
                </div>
                <p class="plan__price">
                    <span class="plan__currency">$</span>
                    <span class="plan__amount">49</span>
                    <span class="plan__period">/{{ __('month') }}</span>
                </p>
                <ul class="plan__features">
                    <li class="plan__feature"><span class="plan__feature-check"><i data-lucide="check"></i></span><strong>{{ __('500 credits') }}</strong> {{ __('a month') }}</li>
                    <li class="plan__feature"><span class="plan__feature-check"><i data-lucide="check"></i></span>{{ __('Up to 16× upscaling') }}</li>
                    <li class="plan__feature"><span class="plan__feature-check"><i data-lucide="check"></i></span>{{ __('Batch processing & 90-day history') }}</li>
                </ul>
                <button type="button" class="btn btn-soft btn-block plan__cta is-disabled" aria-disabled="true">
                    {{ __('Your current plan') }}
                </button>
            </article>

            <article class="plan">
                <div class="plan__head">
                    <h3 class="plan__name">{{ __('Scale') }}</h3>
                    <p class="plan__tagline">{{ __('For catalogues and marketplaces.') }}</p>
                </div>
                <p class="plan__price">
                    <span class="plan__currency">$</span>
                    <span class="plan__amount">199</span>
                    <span class="plan__period">/{{ __('month') }}</span>
                </p>
                <ul class="plan__features">
                    <li class="plan__feature"><span class="plan__feature-check"><i data-lucide="check"></i></span>{{ __('2,500 credits a month') }}</li>
                    <li class="plan__feature"><span class="plan__feature-check"><i data-lucide="check"></i></span>{{ __('Priority GPU queue') }}</li>
                    <li class="plan__feature"><span class="plan__feature-check"><i data-lucide="check"></i></span>{{ __('Unlimited history & SSO') }}</li>
                </ul>
                <button type="button" class="btn btn-primary btn-block plan__cta" data-ripple>{{ __('Upgrade') }}</button>
            </article>
        </div>
    </section>

    <section class="panel mt-xl" aria-labelledby="payment-title">
        <div class="panel__head">
            <h2 class="panel__title" id="payment-title">
                <i data-lucide="credit-card"></i>
                {{ __('Payment method') }}
            </h2>
            <button type="button" class="btn btn-outline btn-sm">
                <i data-lucide="plus"></i>
                {{ __('Add method') }}
            </button>
        </div>

        <div class="panel__body">
            <div class="pay-card">
                <span class="pay-card__brand" aria-hidden="true">VISA</span>
                <span class="pay-card__body">
                    <span class="pay-card__number">&bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; 4242</span>
                    <span class="pay-card__meta">{{ __('Expires 08 / 2028') }} &middot; Marta Kovac</span>
                </span>
                <span class="badge badge-sm badge-success">{{ __('Default') }}</span>
                <button type="button" class="icon-btn" aria-label="{{ __('Remove this card') }}">
                    <i data-lucide="trash-2"></i>
                </button>
            </div>
        </div>

        <div class="panel__foot">
            <p class="panel__note">
                {{ __('Payments are processed by Stripe. We never see or store your card number.') }}
            </p>
        </div>
    </section>

    <section class="panel" aria-labelledby="details-title">
        <div class="panel__head">
            <h2 class="panel__title" id="details-title">
                <i data-lucide="file-text"></i>
                {{ __('Billing details') }}
            </h2>
            <p class="panel__subtitle">{{ __('These appear on every invoice we issue.') }}</p>
        </div>

        <form class="panel__body" action="#" method="post">
            @csrf
            <div class="form-grid form-grid-2">
                <div class="field">
                    <label class="field__label" for="bill-name">{{ __('Billed to') }}</label>
                    <input class="input" type="text" id="bill-name" name="billed_to" value="Northwind Studio Ltd">
                </div>

                <div class="field">
                    <label class="field__label" for="bill-email">{{ __('Billing email') }}</label>
                    <input class="input" type="email" id="bill-email" name="billing_email" value="accounts@northwind.co">
                </div>

                <div class="field form-grid__full">
                    <label class="field__label" for="bill-address">{{ __('Address') }}</label>
                    <input class="input" type="text" id="bill-address" name="address" value="14 Prinsengracht">
                </div>

                <div class="field">
                    <label class="field__label" for="bill-city">{{ __('City') }}</label>
                    <input class="input" type="text" id="bill-city" name="city" value="Amsterdam">
                </div>

                <div class="field">
                    <label class="field__label" for="bill-post">{{ __('Postal code') }}</label>
                    <input class="input" type="text" id="bill-post" name="postcode" value="1015 DV">
                </div>

                <div class="field">
                    <label class="field__label" for="bill-country">{{ __('Country') }}</label>
                    <select class="select" id="bill-country" name="country">
                        <option selected>{{ __('Netherlands') }}</option>
                        <option>{{ __('Germany') }}</option>
                        <option>{{ __('United Kingdom') }}</option>
                        <option>{{ __('United States') }}</option>
                    </select>
                </div>

                <div class="field">
                    <label class="field__label" for="bill-vat">{{ __('VAT number') }}</label>
                    <input class="input" type="text" id="bill-vat" name="vat" value="NL854502130B01">
                    <p class="field__hint">{{ __('Valid EU VAT numbers are charged at 0%.') }}</p>
                </div>
            </div>

            <div class="cluster cluster-sm mt-lg">
                <button type="submit" class="btn btn-primary btn-sm" data-ripple>{{ __('Save details') }}</button>
                <button type="reset" class="btn btn-ghost btn-sm">{{ __('Discard') }}</button>
            </div>
        </form>
    </section>

    <section class="panel" aria-labelledby="invoices-title">
        <div class="panel__head">
            <h2 class="panel__title" id="invoices-title">
                <i data-lucide="download"></i>
                {{ __('Invoices') }}
            </h2>
            <button type="button" class="btn btn-outline btn-sm">
                <i data-lucide="download"></i>
                {{ __('Download all') }}
            </button>
        </div>

        <div class="panel__body panel__body-flush">
            <div class="table-scroll">
                <table class="data-table">
                    <caption class="sr-only">{{ __('Invoice history') }}</caption>
                    <thead>
                        <tr>
                            <th scope="col">{{ __('Invoice') }}</th>
                            <th scope="col">{{ __('Date') }}</th>
                            <th scope="col">{{ __('Description') }}</th>
                            <th scope="col">{{ __('Amount') }}</th>
                            <th scope="col">{{ __('Status') }}</th>
                            <th scope="col"><span class="sr-only">{{ __('Download') }}</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ([
                            ['id' => 'INV-2026-0812', 'date' => '1 Aug 2026', 'desc' => __('Studio plan — monthly'), 'amount' => '$49.00', 'status' => __('Paid'), 'badge' => 'badge-success'],
                            ['id' => 'INV-2026-0788', 'date' => '18 Jul 2026', 'desc' => __('Credit pack — 500 credits'), 'amount' => '$52.00', 'status' => __('Paid'), 'badge' => 'badge-success'],
                            ['id' => 'INV-2026-0740', 'date' => '1 Jul 2026', 'desc' => __('Studio plan — monthly'), 'amount' => '$49.00', 'status' => __('Paid'), 'badge' => 'badge-success'],
                            ['id' => 'INV-2026-0693', 'date' => '1 Jun 2026', 'desc' => __('Studio plan — monthly'), 'amount' => '$49.00', 'status' => __('Refunded'), 'badge' => 'badge-warning'],
                            ['id' => 'INV-2026-0651', 'date' => '1 May 2026', 'desc' => __('Starter plan — monthly'), 'amount' => '$12.00', 'status' => __('Paid'), 'badge' => 'badge-success'],
                        ] as $invoice)
                            <tr>
                                <td class="data-table__strong data-table__num">{{ $invoice['id'] }}</td>
                                <td class="data-table__num">{{ $invoice['date'] }}</td>
                                <td>{{ $invoice['desc'] }}</td>
                                <td class="data-table__num">{{ $invoice['amount'] }}</td>
                                <td><span class="badge badge-sm {{ $invoice['badge'] }}">{{ $invoice['status'] }}</span></td>
                                <td class="data-table__end">
                                    <a class="icon-btn" href="#" aria-label="{{ __('Download invoice :invoice', ['invoice' => $invoice['id']]) }}">
                                        <i data-lucide="download"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel__foot">
            <p class="pagination__meta">{{ __('Showing 5 of 18 invoices') }}</p>

            <nav class="pagination" aria-label="{{ __('Invoice pages') }}">
                <a class="pagination__item is-disabled" href="#" aria-disabled="true" aria-label="{{ __('Previous page') }}">
                    <i data-lucide="chevron-left"></i>
                </a>
                <a class="pagination__item is-active" href="#" aria-current="page">1</a>
                <a class="pagination__item" href="#">2</a>
                <a class="pagination__item" href="#">3</a>
                <span class="pagination__gap">...</span>
                <a class="pagination__item" href="#">4</a>
                <a class="pagination__item" href="#" aria-label="{{ __('Next page') }}">
                    <i data-lucide="chevron-right"></i>
                </a>
            </nav>
        </div>
    </section>
</x-layouts.user>
