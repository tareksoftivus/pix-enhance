<?php

namespace App\Modules\PaymentGateways\Drivers;

use App\Modules\PaymentGateways\Contracts\PaymentGatewayInterface;
use App\Modules\PaymentGateways\DataObjects\PaymentData;
use App\Modules\PaymentGateways\DataObjects\PaymentResponse;
use App\Modules\PaymentGateways\DataObjects\RefundResult;
use App\Modules\PaymentGateways\DataObjects\WebhookResult;
use Illuminate\Http\Request;
use RuntimeException;
use Stripe\Checkout\Session as StripeCheckoutSession;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\SignatureVerificationException;
use Stripe\PaymentIntent;
use Stripe\Refund;
use Stripe\Stripe;
use Stripe\Webhook;

class StripePaymentGateway implements PaymentGatewayInterface
{
    public function name(): string
    {
        return 'stripe';
    }

    /**
     * Ensure required Stripe credentials are configured.
     *
     * @throws RuntimeException
     */
    protected function ensureConfigured(): void
    {
        if (! class_exists(Stripe::class)) {
            throw new RuntimeException(
                'Stripe SDK is not installed. Run: composer require stripe/stripe-php'
            );
        }

        $secretKey = payment_gateway_setting('stripe_secret_key', '');

        if (empty($secretKey)) {
            throw new RuntimeException(
                'Stripe API keys are not configured. Set them in Settings → Payment Gateways.'
            );
        }
    }

    public function createPayment(PaymentData $data): PaymentResponse
    {
        $this->ensureConfigured();

        try {
            $secretKey = payment_gateway_setting('stripe_secret_key', '');
            Stripe::setApiKey($secretKey);

            $metadata = $this->metadataFor($data);
            $session = StripeCheckoutSession::create([
                'mode' => 'payment',
                'line_items' => [[
                    'price_data' => [
                        'currency' => strtolower($data->currency),
                        'product_data' => [
                            'name' => $data->description ?: 'Payment',
                        ],
                        'unit_amount' => (int) round($data->amount * 100),
                    ],
                    'quantity' => 1,
                ]],
                'metadata' => $metadata,
                'payment_intent_data' => [
                    'metadata' => $metadata,
                ],
                'success_url' => $this->successUrl($data),
                'cancel_url' => $data->cancelUrl ?: route('payments.cancel', ['gateway' => $this->name()]),
            ]);

            if (empty($session->url)) {
                return PaymentResponse::failed('Stripe checkout URL was not returned.');
            }

            return PaymentResponse::redirect($session->id, $session->url, [
                'stripe_session_id' => $session->id,
                'stripe_payment_intent_id' => is_string($session->payment_intent ?? null) ? $session->payment_intent : null,
            ]);
        } catch (ApiErrorException $e) {
            return PaymentResponse::failed($e->getMessage());
        }
    }

    public function verifyPayment(Request $request): PaymentResponse
    {
        $this->ensureConfigured();

        try {
            $secretKey = payment_gateway_setting('stripe_secret_key', '');
            $publishableKey = payment_gateway_setting('stripe_publishable_key', '');

            Stripe::setApiKey($secretKey);

            $sessionId = $request->get('session_id');

            if (! empty($sessionId)) {
                $session = StripeCheckoutSession::retrieve([
                    'id' => $sessionId,
                    'expand' => ['payment_intent'],
                ]);

                $paymentIntent = $session->payment_intent ?? null;
                $paymentIntentId = is_string($paymentIntent) ? $paymentIntent : ($paymentIntent->id ?? null);

                return match ($session->payment_status) {
                    'paid' => PaymentResponse::completed($session->id, [
                        'stripe_session_id' => $session->id,
                        'stripe_payment_intent_id' => $paymentIntentId,
                        'amount' => ($session->amount_total ?? 0) / 100,
                        'currency' => $session->currency,
                    ]),
                    'unpaid', 'no_payment_required' => PaymentResponse::pending($session->id, [
                        'stripe_session_id' => $session->id,
                        'stripe_payment_intent_id' => $paymentIntentId,
                        'payment_status' => $session->payment_status,
                    ]),
                    default => PaymentResponse::failed("Unexpected Stripe checkout status: {$session->payment_status}"),
                };
            }

            $paymentIntentId = $request->get('payment_intent');

            if (empty($paymentIntentId)) {
                return PaymentResponse::failed('Missing Stripe payment identifier.');
            }

            $intent = PaymentIntent::retrieve($paymentIntentId);

            return match ($intent->status) {
                'succeeded' => PaymentResponse::completed($intent->id, [
                    'amount' => $intent->amount / 100,
                    'currency' => $intent->currency,
                    'payment_method' => $intent->payment_method,
                ]),
                'requires_action', 'requires_confirmation' => PaymentResponse::clientAction($intent->id, [
                    'client_secret' => $intent->client_secret,
                    'publishable_key' => $publishableKey,
                ]),
                'canceled' => PaymentResponse::failed('Payment was canceled.'),
                default => PaymentResponse::failed("Unexpected payment status: {$intent->status}"),
            };
        } catch (ApiErrorException $e) {
            return PaymentResponse::failed($e->getMessage());
        }
    }

    public function refund(string $gatewayPaymentId, float $amount, string $reason = ''): RefundResult
    {
        $this->ensureConfigured();

        try {
            $secretKey = payment_gateway_setting('stripe_secret_key', '');

            Stripe::setApiKey($secretKey);

            if (str_starts_with($gatewayPaymentId, 'cs_')) {
                $session = StripeCheckoutSession::retrieve($gatewayPaymentId);
                $gatewayPaymentId = (string) ($session->payment_intent ?? '');
            }

            if (empty($gatewayPaymentId)) {
                return RefundResult::failed('Stripe payment intent was not found for this payment.');
            }

            $params = [
                'payment_intent' => $gatewayPaymentId,
                'amount' => (int) round($amount * 100),
            ];

            if (! empty($reason)) {
                $params['reason'] = 'requested_by_customer';
                $params['metadata'] = ['reason' => $reason];
            }

            $refund = Refund::create($params);

            return match ($refund->status) {
                'succeeded' => RefundResult::success($refund->id, 'completed', [
                    'amount' => $refund->amount / 100,
                    'currency' => $refund->currency,
                ]),
                'pending' => RefundResult::pending($refund->id, [
                    'amount' => $refund->amount / 100,
                    'currency' => $refund->currency,
                ]),
                default => RefundResult::failed("Refund status: {$refund->status}"),
            };
        } catch (ApiErrorException $e) {
            return RefundResult::failed($e->getMessage());
        }
    }

    public function verifyWebhook(Request $request): bool
    {
        $webhookSecret = payment_gateway_setting('stripe_webhook_secret', '');

        if (empty($webhookSecret)) {
            return false;
        }

        try {
            $signature = $request->header('Stripe-Signature', '');

            Webhook::constructEvent(
                $request->getContent(),
                $signature,
                $webhookSecret
            );

            return true;
        } catch (SignatureVerificationException) {
            return false;
        }
    }

    public function handleWebhook(Request $request): WebhookResult
    {
        $payload = $request->all();
        $eventType = $payload['type'] ?? 'unknown';
        $object = $payload['data']['object'] ?? [];

        $gatewayPaymentId = $object['id'] ?? null;

        $status = match ($eventType) {
            'checkout.session.completed' => 'completed',
            'checkout.session.async_payment_failed' => 'failed',
            'checkout.session.expired' => 'canceled',
            'payment_intent.succeeded' => 'completed',
            'payment_intent.payment_failed' => 'failed',
            'payment_intent.canceled' => 'canceled',
            'charge.refunded' => 'refunded',
            default => null,
        };

        return new WebhookResult(
            gatewayPaymentId: $gatewayPaymentId,
            status: $status,
            eventType: $eventType,
            metadata: array_merge($object, array_filter([
                'stripe_session_id' => str_starts_with((string) $gatewayPaymentId, 'cs_') ? $gatewayPaymentId : null,
                'stripe_payment_intent_id' => $object['payment_intent'] ?? null,
            ])),
        );
    }

    public function getClientConfig(): array
    {
        return [
            'publishable_key' => payment_gateway_setting('stripe_publishable_key', ''),
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function metadataFor(PaymentData $data): array
    {
        $metadata = array_merge($data->metadata, array_filter([
            'user_id' => $data->userId,
            'user_type' => $data->userType,
            'description' => $data->description,
        ], fn ($value): bool => $value !== null && $value !== ''));

        return collect($metadata)
            ->filter(fn ($value): bool => is_scalar($value) && $value !== null && $value !== '')
            ->map(fn ($value): string => is_bool($value) ? ($value ? 'true' : 'false') : (string) $value)
            ->all();
    }

    protected function successUrl(PaymentData $data): string
    {
        $url = $data->returnUrl ?: route('payments.return', ['gateway' => $this->name()]);
        $separator = str_contains($url, '?') ? '&' : '?';

        return $url.$separator.'session_id={CHECKOUT_SESSION_ID}';
    }
}
