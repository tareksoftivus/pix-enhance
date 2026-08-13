<?php

use App\Modules\PaymentGateways\DataObjects\PaymentData;
use App\Modules\PaymentGateways\Drivers\BitPayPaymentGateway;
use App\Modules\PaymentGateways\Drivers\CoinbaseCommercePaymentGateway;
use App\Modules\PaymentGateways\Drivers\IzipayPaymentGateway;
use App\Modules\PaymentGateways\Drivers\MercadoPagoPaymentGateway;
use App\Modules\PaymentGateways\Drivers\MolliePaymentGateway;
use App\Modules\PaymentGateways\Drivers\NowPaymentsPaymentGateway;
use App\Modules\PaymentGateways\Drivers\XenditPaymentGateway;
use App\Modules\PaymentGateways\Services\PaymentGatewayManager;
use App\Modules\PaymentGatewaySettings\Services\PaymentGatewaySettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

function setGatewaySetting(string $key, mixed $value): void
{
    app(PaymentGatewaySettingsService::class)->set($key, $value);
}

it('resolves the mercadopago driver from the manager', function () {
    expect(app(PaymentGatewayManager::class)->driver('mercadopago'))
        ->toBeInstanceOf(MercadoPagoPaymentGateway::class);
});

it('resolves the izipay driver from the manager', function () {
    expect(app(PaymentGatewayManager::class)->driver('izipay'))
        ->toBeInstanceOf(IzipayPaymentGateway::class);
});

it('resolves the mollie driver from the manager', function () {
    expect(app(PaymentGatewayManager::class)->driver('mollie'))
        ->toBeInstanceOf(MolliePaymentGateway::class);
});

it('resolves the xendit driver from the manager', function () {
    expect(app(PaymentGatewayManager::class)->driver('xendit'))
        ->toBeInstanceOf(XenditPaymentGateway::class);
});

it('resolves the nowpayments driver from the manager', function () {
    expect(app(PaymentGatewayManager::class)->driver('nowpayments'))
        ->toBeInstanceOf(NowPaymentsPaymentGateway::class);
});

it('resolves the coinbase commerce driver from the manager', function () {
    expect(app(PaymentGatewayManager::class)->driver('coinbasecommerce'))
        ->toBeInstanceOf(CoinbaseCommercePaymentGateway::class);
});

it('resolves the bitpay driver from the manager', function () {
    expect(app(PaymentGatewayManager::class)->driver('bitpay'))
        ->toBeInstanceOf(BitPayPaymentGateway::class);
});

it('lists new gateways as enabled when toggled on', function () {
    foreach (['mercadopago', 'izipay', 'mollie', 'xendit', 'nowpayments', 'coinbasecommerce', 'bitpay'] as $slug) {
        setGatewaySetting("{$slug}_enabled", true);
    }

    $enabled = app(PaymentGatewayManager::class)->getEnabledGatewayNames();

    expect($enabled)
        ->toContain('mercadopago')->toContain('izipay')->toContain('mollie')
        ->toContain('xendit')->toContain('nowpayments')
        ->toContain('coinbasecommerce')->toContain('bitpay');
});

it('exposes only public client config for mercadopago', function () {
    setGatewaySetting('mercadopago_public_key', 'TEST-public-123');
    setGatewaySetting('mercadopago_access_token', 'TEST-secret-should-not-leak');

    $config = (new MercadoPagoPaymentGateway)->getClientConfig();

    expect($config)
        ->toHaveKey('public_key', 'TEST-public-123')
        ->and($config)->not->toHaveKey('access_token');
});

it('creates a mercadopago preference and returns a redirect', function () {
    setGatewaySetting('mercadopago_access_token', 'TEST-token');
    setGatewaySetting('mercadopago_sandbox', true);

    Http::fake([
        'api.mercadopago.com/checkout/preferences' => Http::response([
            'id' => 'pref_123',
            'init_point' => 'https://mp.com/live',
            'sandbox_init_point' => 'https://mp.com/sandbox',
        ], 201),
    ]);

    $response = (new MercadoPagoPaymentGateway)->createPayment(
        new PaymentData(amount: 50.0, currency: 'PEN', description: 'Test')
    );

    expect($response->isRedirect())->toBeTrue()
        ->and($response->redirectUrl)->toBe('https://mp.com/sandbox');
});

it('fails mercadopago payment when access token is missing', function () {
    expect(fn () => (new MercadoPagoPaymentGateway)->createPayment(PaymentData::make(10.0)))
        ->toThrow(RuntimeException::class);
});

it('creates an izipay form token and returns a client action', function () {
    setGatewaySetting('izipay_shop_id', 'shop_1');
    setGatewaySetting('izipay_api_password', 'testpassword_abc');
    setGatewaySetting('izipay_public_key', 'testpublickey_abc');

    Http::fake([
        'api.micuentaweb.pe/api-payment/V4/Charge/CreatePayment' => Http::response([
            'status' => 'SUCCESS',
            'answer' => ['formToken' => 'form_token_xyz'],
        ]),
    ]);

    $response = (new IzipayPaymentGateway)->createPayment(
        new PaymentData(amount: 99.9, currency: 'PEN')
    );

    expect($response->requiresClientAction())->toBeTrue()
        ->and($response->clientData['form_token'])->toBe('form_token_xyz');
});

it('rejects an izipay return with an invalid signature', function () {
    setGatewaySetting('izipay_hmac_key', 'correct-key');

    $answer = json_encode(['orderStatus' => 'PAID', 'orderDetails' => ['orderId' => 'izi_1']]);

    $request = Request::create('/return', 'POST', [
        'kr-answer' => $answer,
        'kr-hash' => 'tampered-hash',
    ]);

    $response = (new IzipayPaymentGateway)->verifyPayment($request);

    expect($response->isFailed())->toBeTrue();
});

it('accepts an izipay return with a valid signature', function () {
    setGatewaySetting('izipay_hmac_key', 'correct-key');

    $answer = json_encode([
        'orderStatus' => 'PAID',
        'orderDetails' => ['orderId' => 'izi_1'],
        'transactions' => [['uuid' => 'txn_1', 'amount' => 9990, 'currency' => 'PEN']],
    ]);
    $validHash = hash_hmac('sha256', $answer, 'correct-key');

    $request = Request::create('/return', 'POST', [
        'kr-answer' => $answer,
        'kr-hash' => $validHash,
    ]);

    $response = (new IzipayPaymentGateway)->verifyPayment($request);

    expect($response->isComplete())->toBeTrue()
        ->and($response->gatewayPaymentId)->toBe('izi_1');
});

it('creates a mollie payment and returns the checkout redirect', function () {
    setGatewaySetting('mollie_api_key', 'test_abc123');

    Http::fake([
        'api.mollie.com/v2/payments' => Http::response([
            'id' => 'tr_123',
            'status' => 'open',
            '_links' => ['checkout' => ['href' => 'https://mollie.com/checkout/tr_123']],
        ], 201),
    ]);

    $response = (new MolliePaymentGateway)->createPayment(
        new PaymentData(amount: 12.5, currency: 'EUR', description: 'Test')
    );

    expect($response->isRedirect())->toBeTrue()
        ->and($response->redirectUrl)->toBe('https://mollie.com/checkout/tr_123')
        ->and($response->gatewayPaymentId)->toBe('tr_123');
});

it('verifies a paid mollie payment by re-fetching status', function () {
    setGatewaySetting('mollie_api_key', 'test_abc123');

    Http::fake([
        'api.mollie.com/v2/payments/tr_123' => Http::response([
            'id' => 'tr_123',
            'status' => 'paid',
            'amount' => ['currency' => 'EUR', 'value' => '12.50'],
            'metadata' => ['reference' => 'mol_ref_1'],
        ]),
    ]);

    $request = Request::create('/return', 'GET', ['id' => 'tr_123']);

    $response = (new MolliePaymentGateway)->verifyPayment($request);

    expect($response->isComplete())->toBeTrue()
        ->and($response->gatewayPaymentId)->toBe('mol_ref_1');
});

it('fails mollie payment when api key is missing', function () {
    expect(fn () => (new MolliePaymentGateway)->createPayment(PaymentData::make(10.0, 'EUR')))
        ->toThrow(RuntimeException::class);
});

it('creates a xendit invoice and returns a redirect', function () {
    setGatewaySetting('xendit_secret_key', 'xnd_test_123');

    Http::fake([
        'api.xendit.co/v2/invoices' => Http::response([
            'id' => 'inv_1',
            'external_id' => 'xnd_ref_1',
            'invoice_url' => 'https://checkout.xendit.co/inv_1',
            'status' => 'PENDING',
        ], 200),
    ]);

    $response = (new XenditPaymentGateway)->createPayment(
        new PaymentData(amount: 100.0, currency: 'IDR', description: 'Test')
    );

    expect($response->isRedirect())->toBeTrue()
        ->and($response->redirectUrl)->toBe('https://checkout.xendit.co/inv_1');
});

it('verifies xendit webhook by callback token', function () {
    setGatewaySetting('xendit_webhook_token', 'secret-token');

    $good = Request::create('/wh', 'POST', [], [], [], ['HTTP_X_CALLBACK_TOKEN' => 'secret-token']);
    $bad = Request::create('/wh', 'POST', [], [], [], ['HTTP_X_CALLBACK_TOKEN' => 'wrong']);

    expect((new XenditPaymentGateway)->verifyWebhook($good))->toBeTrue()
        ->and((new XenditPaymentGateway)->verifyWebhook($bad))->toBeFalse();
});

it('creates a nowpayments invoice and returns a redirect', function () {
    setGatewaySetting('nowpayments_api_key', 'np_test_123');

    Http::fake([
        'api.nowpayments.io/v1/invoice' => Http::response([
            'id' => '4552',
            'invoice_url' => 'https://nowpayments.io/payment/?iid=4552',
        ], 200),
    ]);

    $response = (new NowPaymentsPaymentGateway)->createPayment(
        new PaymentData(amount: 25.0, currency: 'USD')
    );

    expect($response->isRedirect())->toBeTrue()
        ->and($response->redirectUrl)->toBe('https://nowpayments.io/payment/?iid=4552');
});

it('verifies nowpayments ipn signature over sorted payload', function () {
    setGatewaySetting('nowpayments_ipn_secret', 'ipn-secret');

    $payload = ['payment_status' => 'finished', 'order_id' => 'now_1', 'payment_id' => 99];
    ksort($payload);
    $sorted = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    $sig = hash_hmac('sha512', $sorted, 'ipn-secret');

    $request = Request::create('/wh', 'POST', $payload, [], [], ['HTTP_X_NOWPAYMENTS_SIG' => $sig]);

    expect((new NowPaymentsPaymentGateway)->verifyWebhook($request))->toBeTrue();
});

it('creates a coinbase commerce charge and returns a redirect', function () {
    setGatewaySetting('coinbasecommerce_api_key', 'cbc_key');

    Http::fake([
        'api.commerce.coinbase.com/charges' => Http::response([
            'data' => [
                'id' => 'charge_1',
                'code' => 'ABC123',
                'hosted_url' => 'https://commerce.coinbase.com/charges/ABC123',
            ],
        ], 201),
    ]);

    $response = (new CoinbaseCommercePaymentGateway)->createPayment(
        new PaymentData(amount: 40.0, currency: 'USD', description: 'Test')
    );

    expect($response->isRedirect())->toBeTrue()
        ->and($response->redirectUrl)->toBe('https://commerce.coinbase.com/charges/ABC123');
});

it('verifies coinbase commerce webhook signature over the raw body', function () {
    setGatewaySetting('coinbasecommerce_webhook_secret', 'wh-secret');

    $body = json_encode(['event' => ['type' => 'charge:confirmed', 'data' => ['id' => 'charge_1']]]);
    $sig = hash_hmac('sha256', $body, 'wh-secret');

    $request = Request::create('/wh', 'POST', [], [], [], ['HTTP_X_CC_WEBHOOK_SIGNATURE' => $sig], $body);

    expect((new CoinbaseCommercePaymentGateway)->verifyWebhook($request))->toBeTrue();
});

it('creates a bitpay invoice and returns a redirect', function () {
    setGatewaySetting('bitpay_api_token', 'bitpay_token');
    setGatewaySetting('bitpay_sandbox', true);

    Http::fake([
        'test.bitpay.com/invoices' => Http::response([
            'data' => [
                'id' => 'inv_btp_1',
                'url' => 'https://test.bitpay.com/invoice?id=inv_btp_1',
                'status' => 'new',
            ],
        ], 200),
    ]);

    $response = (new BitPayPaymentGateway)->createPayment(
        new PaymentData(amount: 60.0, currency: 'USD', description: 'Test')
    );

    expect($response->isRedirect())->toBeTrue()
        ->and($response->redirectUrl)->toBe('https://test.bitpay.com/invoice?id=inv_btp_1');
});

it('reports no automated refunds for crypto processors', function () {
    expect((new NowPaymentsPaymentGateway)->refund('x', 10.0)->success)->toBeFalse()
        ->and((new CoinbaseCommercePaymentGateway)->refund('x', 10.0)->success)->toBeFalse();
});
