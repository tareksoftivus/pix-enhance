<x-layouts.user :title="__('Complete Payment')" :search-placeholder="__('Search payments')">
    @php
        $gatewayLabel = \Illuminate\Support\Str::headline($payment->gateway);
        $amount = number_format((float) $payment->amount, 2).' '.$payment->currency;
    @endphp

    <div class="dash__head">
        <div>
            <h1 class="dash__title">{{ __('Complete Payment') }}</h1>
            <p class="dash__subtitle">
                {{ __('Finish your :gateway payment for :amount.', ['gateway' => $gatewayLabel, 'amount' => $amount]) }}
            </p>
        </div>

        <div class="cluster cluster-sm">
            <a class="btn btn-outline btn-sm" href="{{ route('user.billing') }}">
                <i data-lucide="arrow-left"></i>
                {{ __('Back to billing') }}
            </a>
        </div>
    </div>

    <section class="panel">
        <div class="panel__head">
            <h2 class="panel__title">
                <i data-lucide="shield-check"></i>
                {{ __('Secure payment') }}
            </h2>
            <p class="panel__subtitle">
                {{ __('Your payment details are handled by the payment provider. We only receive the signed confirmation.') }}
            </p>
        </div>

        <div class="panel__body">
            @if ($payment->gateway === 'razorpay')
                <div class="empty-state">
                    <span class="empty-state__icon" aria-hidden="true"><i data-lucide="credit-card"></i></span>
                    <h3>{{ __('Razorpay checkout') }}</h3>
                    <p>{{ __('The secure Razorpay window should open automatically.') }}</p>
                    <button type="button" class="btn btn-primary" id="razorpay-pay-button" data-ripple>
                        <i data-lucide="lock"></i>
                        {{ __('Pay :amount', ['amount' => $amount]) }}
                    </button>
                </div>

                <form id="razorpay-complete-form" method="post" action="{{ route('payments.action.complete', $payment->uuid) }}">
                    <input type="hidden" name="razorpay_order_id" value="{{ $clientData['order_id'] ?? '' }}">
                    <input type="hidden" name="razorpay_payment_id" value="">
                    <input type="hidden" name="razorpay_signature" value="">
                </form>
            @elseif ($payment->gateway === 'izipay')
                <div class="form-grid">
                    <div class="form-grid__full">
                        <div class="kr-embedded" kr-form-token="{{ $clientData['form_token'] ?? '' }}">
                            <button class="kr-payment-button"></button>
                            <div class="kr-form-error"></div>
                        </div>
                    </div>
                </div>
            @else
                <div class="empty-state">
                    <span class="empty-state__icon" aria-hidden="true"><i data-lucide="triangle-alert"></i></span>
                    <h3>{{ __('Payment action unavailable') }}</h3>
                    <p>{{ __('This gateway did not provide a supported browser payment action.') }}</p>
                    <a class="btn btn-primary" href="{{ route('user.billing') }}">
                        <i data-lucide="arrow-left"></i>
                        {{ __('Return to billing') }}
                    </a>
                </div>
            @endif
        </div>
    </section>

    @push('styles')
        @if ($payment->gateway === 'izipay')
            <link rel="stylesheet" href="https://static.micuentaweb.pe/static/js/krypton-client/V4.0/ext/classic.css">
        @endif
    @endpush

    @push('scripts')
        @if ($payment->gateway === 'razorpay')
            <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
            <script>
                (() => {
                    const button = document.getElementById('razorpay-pay-button');
                    const form = document.getElementById('razorpay-complete-form');

                    const openCheckout = () => {
                        if (!window.Razorpay || !form) {
                            return;
                        }

                        const checkout = new window.Razorpay({
                            key: @js($clientData['key_id'] ?? ''),
                            amount: @js($clientData['amount'] ?? null),
                            currency: @js($clientData['currency'] ?? $payment->currency),
                            name: @js($clientData['name'] ?? config('app.name')),
                            description: @js($clientData['description'] ?? $payment->description),
                            order_id: @js($clientData['order_id'] ?? ''),
                            handler(response) {
                                form.querySelector('[name="razorpay_payment_id"]').value = response.razorpay_payment_id || '';
                                form.querySelector('[name="razorpay_signature"]').value = response.razorpay_signature || '';
                                form.submit();
                            },
                            modal: {
                                ondismiss() {
                                    button?.removeAttribute('disabled');
                                },
                            },
                            prefill: {
                                name: @js(auth()->user()?->name),
                                email: @js(auth()->user()?->email),
                            },
                            theme: {
                                color: '#111827',
                            },
                        });

                        button?.setAttribute('disabled', 'disabled');
                        checkout.open();
                    };

                    button?.addEventListener('click', openCheckout);
                    window.addEventListener('load', openCheckout);
                })();
            </script>
        @elseif ($payment->gateway === 'izipay')
            <script
                src="https://static.micuentaweb.pe/static/js/krypton-client/V4.0/stable/kr-payment-form.min.js"
                kr-public-key="{{ $clientData['public_key'] ?? '' }}"
                kr-post-url="{{ route('payments.action.complete', $payment->uuid) }}">
            </script>
        @endif
    @endpush
</x-layouts.user>
