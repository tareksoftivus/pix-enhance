<x-layouts.user :title="__('Checkout')" :search-placeholder="__('Search payments')">
    @php
        $formAction = $order['type'] === 'plan'
            ? route('user.credits.plans.purchase', $order['id'])
            : route('user.credits.packs.purchase');
        $available = $creditSummary['available'] ?? 0;
        $isFree = $order['price'] <= 0;
        $queryFor = fn (string $gateway) => array_filter([
            'type' => $order['type'],
            'pack' => $order['type'] === 'pack' ? $order['slug'] : null,
            'plan' => $order['type'] === 'plan' ? $order['id'] : null,
            'gateway' => $gateway,
        ], fn ($value) => $value !== null);
    @endphp

    <div class="dash__head">
        <div>
            <h1 class="dash__title">{{ __('Checkout') }}</h1>
            <p class="dash__subtitle">
                {{ __('Choose how to pay, review your order and confirm.') }}
            </p>
        </div>

        <div class="cluster cluster-sm">
            <a class="btn btn-outline btn-sm" href="{{ route('user.billing') }}">
                <i data-lucide="arrow-left"></i>
                {{ __('Back to billing') }}
            </a>
        </div>
    </div>

    <div class="checkout-grid">
        <section class="panel" aria-labelledby="checkout-details-title">
            <div class="panel__head">
                <h2 class="panel__title" id="checkout-details-title">
                    <i data-lucide="credit-card"></i>
                    {{ __('Payment method') }}
                </h2>
                <p class="panel__subtitle">
                    {{ __('Each gateway may add its own processing fee, set by the site admin.') }}
                </p>
            </div>

            <div class="panel__body">
                @if ($isFree)
                    <div class="empty-state">
                        <span class="empty-state__icon" aria-hidden="true"><i data-lucide="gift"></i></span>
                        <h3>{{ __('No payment required') }}</h3>
                        <p>{{ __('This plan is free — credits are added to your wallet immediately.') }}</p>
                    </div>
                @elseif (empty($order['gateways']))
                    <div class="empty-state">
                        <span class="empty-state__icon" aria-hidden="true"><i data-lucide="triangle-alert"></i></span>
                        <h3>{{ __('No payment gateway available') }}</h3>
                        <p>{{ __('Ask an administrator to enable a payment gateway before checking out.') }}</p>
                    </div>
                @else
                    <div class="checkout-methods">
                        @foreach ($order['gateways'] as $gateway)
                            <a
                                href="{{ route('user.checkout', $queryFor($gateway['name'])) }}"
                                class="checkout-method {{ $gateway['name'] === $order['gateway'] ? 'is-selected' : '' }}"
                            >
                                <span class="checkout-method__body">
                                    @if ($gateway['icon'])
                                        <i class="{{ $gateway['icon'] }}"></i>
                                    @else
                                        <i data-lucide="credit-card"></i>
                                    @endif
                                    <span>
                                        <span class="checkout-method__name">{{ $gateway['label'] }}</span>
                                        <span class="checkout-method__fee">
                                            {{ $gateway['fee'] > 0
                                                ? __('+ :fee fee', ['fee' => $order['currency'].' '.number_format($gateway['fee'], 2)])
                                                : __('No extra fee') }}
                                        </span>
                                    </span>
                                </span>
                                @if ($gateway['name'] === $order['gateway'])
                                    <i data-lucide="circle-check" class="checkout-method__check"></i>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <form class="panel__body" method="post" action="{{ $formAction }}">
                @csrf

                @if ($order['type'] === 'pack')
                    <input type="hidden" name="pack" value="{{ $order['slug'] }}">
                @endif
                <input type="hidden" name="gateway" value="{{ $order['gateway'] }}">

                <div class="field form-grid__full">
                    <label class="field__label" for="checkout-email">{{ __('Receipt email') }}</label>
                    <input class="input" type="email" id="checkout-email" name="email" value="{{ auth()->user()->email ?? '' }}" readonly>
                    <p class="field__hint">{{ __('Your invoice and receipt are sent to this address.') }}</p>
                </div>

                <label class="checkout-terms mt-lg">
                    <input type="checkbox" name="agree_terms" required>
                    <span>{!! __('I agree to the :terms and :refund.', ['terms' => '<a href="#">'.__('Terms of Service').'</a>', 'refund' => '<a href="#">'.__('Refund Policy').'</a>']) !!}</span>
                </label>

                <div class="panel__foot">
                    @if (! $isFree)
                        <p class="panel__note">
                            {{ __('You will be redirected to complete payment if your gateway requires it. Credits are added once payment is confirmed.') }}
                        </p>
                    @else
                        <p class="panel__note">{{ __('This plan is free — credits are added to your wallet immediately, no payment required.') }}</p>
                    @endif

                    <button type="submit" class="btn btn-primary btn-block" data-ripple @disabled(! $isFree && ! $order['gateway'])>
                        <i data-lucide="lock"></i>
                        @if (! $isFree)
                            {{ __('Pay :amount & confirm order', ['amount' => $order['currency'].' '.number_format($order['total'], 2)]) }}
                        @else
                            {{ __('Confirm order') }}
                        @endif
                    </button>
                </div>
            </form>
        </section>

        <aside class="panel checkout-summary" aria-labelledby="checkout-summary-title">
            <div class="panel__head">
                <h2 class="panel__title" id="checkout-summary-title">
                    <i data-lucide="receipt"></i>
                    {{ __('Order summary') }}
                </h2>
            </div>

            <div class="panel__body">
                <div class="checkout-summary__item">
                    <div>
                        <span class="checkout-summary__name">{{ $order['name'] }}</span>
                        <span class="setting-row__hint">
                            @if ($order['type'] === 'plan')
                                {{ __(':credits credits a month · plan subscription', ['credits' => number_format($order['credits'])]) }}
                            @else
                                {{ __(':credits credits · one-time top-up', ['credits' => number_format($order['credits'])]) }}
                            @endif
                        </span>
                        @if ($order['badge'])
                            <span class="badge badge-sm badge-primary mt-xs">{{ __($order['badge']) }}</span>
                        @endif
                    </div>
                    <span class="checkout-summary__price">
                        @if (! $isFree)
                            {{ $order['currency'].' '.number_format($order['price'], 2) }}
                        @else
                            {{ __('Free') }}
                        @endif
                    </span>
                </div>

                <div class="checkout-summary__row">
                    <span>{{ __('Subtotal') }}</span>
                    <span>{{ $order['currency'].' '.number_format($order['price'], 2) }}</span>
                </div>
                <div class="checkout-summary__row">
                    <span>{{ __('Gateway fee') }}</span>
                    <span>{{ $order['currency'].' '.number_format($order['fee'], 2) }}</span>
                </div>
                <div class="checkout-summary__row checkout-summary__row-total">
                    <span>{{ __('Total due today') }}</span>
                    <span>{{ $order['currency'].' '.number_format($order['total'], 2) }}</span>
                </div>

                <div class="checkout-summary__row mt-md">
                    <span>{{ __('Current wallet balance') }}</span>
                    <span>{{ number_format($available) }} {{ __('credits') }}</span>
                </div>
                <div class="checkout-summary__row">
                    <span>{{ __('Balance after purchase') }}</span>
                    <span>{{ number_format($available + $order['credits']) }} {{ __('credits') }}</span>
                </div>

                <div class="checkout-summary__note">
                    <i data-lucide="shield-check"></i>
                    <span>{{ __('Credits are added to your wallet as soon as payment is confirmed. A receipt invoice appears in Billing.') }}</span>
                </div>
            </div>
        </aside>
    </div>
</x-layouts.user>
