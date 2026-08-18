<?php

use App\Models\User;
use App\Modules\Billing\Models\BillingInvoice;
use App\Modules\Billing\Services\BillingService;
use App\Modules\PaymentGateways\Models\Payment;
use App\Modules\PaymentGateways\Models\Refund;
use App\Modules\Shared\Support\ModuleRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('registers the billing module and routes', function () {
    $module = app(ModuleRegistry::class)->find('billing');

    expect($module)->not->toBeNull()
        ->and($module['descriptor'])->not->toBeNull()
        ->and(Route::has('admin.billing.index'))->toBeTrue()
        ->and(Route::has('admin.billing.invoices.show'))->toBeTrue()
        ->and(Route::has('user.billing.invoices.show'))->toBeTrue()
        ->and(Route::has('user.billing.payments.show'))->toBeTrue();
});

it('creates a paid invoice from a completed payment', function () {
    $user = User::factory()->create();
    $payment = billingPaymentFor($user, [
        'status' => 'completed',
        'paid_at' => now(),
        'metadata' => [
            'credits_module' => true,
            'credits_reason' => 'credit_pack_purchase',
            'credits' => 500,
            'credit_pack_name' => 'Creator top-up',
        ],
    ]);

    $invoice = app(BillingService::class)->invoiceForPayment($payment);

    expect($invoice->status)->toBe(BillingInvoice::STATUS_PAID)
        ->and((float) $invoice->total)->toBe(19.0)
        ->and((float) $invoice->amount_paid)->toBe(19.0)
        ->and($invoice->line_items[0]['name'])->toBe('Creator top-up')
        ->and($invoice->line_items[0]['credits'])->toBe(500);
});

it('updates invoice refund totals from payment refunds', function () {
    $user = User::factory()->create();
    $payment = billingPaymentFor($user, [
        'status' => 'refunded',
        'paid_at' => now(),
    ]);

    Refund::create([
        'payment_id' => $payment->id,
        'gateway_refund_id' => 'refund-test',
        'amount' => 5,
        'status' => 'completed',
        'reason' => 'Customer request',
    ]);

    $invoice = app(BillingService::class)->invoiceForPayment($payment->fresh('refunds'));

    expect($invoice->status)->toBe(BillingInvoice::STATUS_REFUNDED)
        ->and((float) $invoice->amount_refunded)->toBe(5.0);
});

it('prevents users from downloading another customer invoice', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $payment = billingPaymentFor($owner, ['status' => 'completed', 'paid_at' => now()]);
    $invoice = app(BillingService::class)->invoiceForPayment($payment);

    $this->actingAs($other)
        ->get(route('user.billing.invoices.download', $invoice))
        ->assertForbidden();
});

if (! function_exists('billingPaymentFor')) {
    function billingPaymentFor(User $user, array $overrides = []): Payment
    {
        return Payment::create(array_merge([
            'uuid' => (string) Str::uuid(),
            'user_type' => $user->getMorphClass(),
            'user_id' => $user->id,
            'gateway' => 'log',
            'gateway_payment_id' => 'log-'.Str::random(8),
            'amount' => 19,
            'currency' => 'USD',
            'status' => 'pending',
            'payment_method' => 'card',
            'description' => 'Credit checkout',
            'metadata' => [
                'credits_module' => true,
                'credits' => 200,
                'credit_pack_name' => 'Starter top-up',
            ],
        ], $overrides));
    }
}
