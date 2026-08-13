<?php

namespace App\Modules\PaymentGateways\Drivers;

use App\Modules\PaymentGateways\Contracts\PaymentGatewayInterface;
use App\Modules\PaymentGateways\DataObjects\PaymentData;
use App\Modules\PaymentGateways\DataObjects\PaymentResponse;
use App\Modules\PaymentGateways\DataObjects\RefundResult;
use App\Modules\PaymentGateways\DataObjects\WebhookResult;
use Illuminate\Http\Request;
use RuntimeException;
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
            $publishableKey = payment_gateway_setting('stripe_publishable_key', '');

            Stripe::setApiKey($secretKey);

            $params = [
                'amount' => (int) round($data->amount * 100),
                'currency' => strtolower($data->currency),
                'metadata' => array_merge($data->metadata, array_filter([
                    'user_id' => $data->userId,
                    'user_type' => $data->userType,
                    'description' => $data->description,
                ])),
            ];

            if ($data->paymentMethod) {
                $params['payment_method'] = $data->paymentMethod;
            }

            $intent = PaymentIntent::create($params);

            return PaymentResponse::clientAction($intent->id, [
                'client_secret' => $intent->client_secret,
                'publishable_key' => $publishableKey,
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

            $paymentIntentId = $request->get('payment_intent');

            if (empty($paymentIntentId)) {
                return PaymentResponse::failed('Missing payment_intent parameter.');
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
            metadata: $object,
        );
    }

    public function getClientConfig(): array
    {
        return [
            'publishable_key' => payment_gateway_setting('stripe_publishable_key', ''),
        ];
    }
}
