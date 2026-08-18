<?php

use App\Models\User;
use App\Modules\Credits\Exceptions\InsufficientCreditsException;
use App\Modules\Credits\Listeners\GrantCreditsForSuccessfulPayment;
use App\Modules\Credits\Models\CreditTransaction;
use App\Modules\Credits\Services\CreditService;
use App\Modules\PaymentGateways\Events\PaymentSucceeded;
use App\Modules\PaymentGateways\Models\Payment;
use App\Modules\Shared\Support\ModuleRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('registers the credits module and routes', function () {
    $module = app(ModuleRegistry::class)->find('credits');

    expect($module)->not->toBeNull()
        ->and($module['descriptor'])->not->toBeNull()
        ->and(Route::has('admin.credits.index'))->toBeTrue()
        ->and(Route::has('user.credits.packs.purchase'))->toBeTrue()
        ->and(Route::has('user.credits.plans.purchase'))->toBeTrue();
});

it('grants and spends credits through the wallet ledger', function () {
    $user = User::factory()->create();
    $credits = app(CreditService::class);

    $grant = $credits->grant($user, 25, 'test_grant');
    $spend = $credits->spend($user, 7, 'test_spend');

    expect($grant->balance_after)->toBe(25)
        ->and($spend->balance_after)->toBe(18)
        ->and($credits->summaryFor($user)['available'])->toBe(18)
        ->and(CreditTransaction::query()->forUser($user->id)->count())->toBe(2);
});

it('prevents overspending available credits', function () {
    $user = User::factory()->create();
    $credits = app(CreditService::class);

    $credits->grant($user, 3, 'test_grant');

    expect(fn () => $credits->spend($user, 4, 'test_spend'))
        ->toThrow(InsufficientCreditsException::class);
});

it('reserves, captures and releases credits', function () {
    $user = User::factory()->create();
    $credits = app(CreditService::class);

    $credits->grant($user, 20, 'test_grant');
    $reservation = $credits->reserve($user, 6, null, ['job' => 'preview'], 'job-1');

    expect($credits->summaryFor($user)['available'])->toBe(14)
        ->and($credits->summaryFor($user)['reserved'])->toBe(6);

    $captured = $credits->capture($reservation);

    expect($captured->amount)->toBe(-6)
        ->and($credits->summaryFor($user)['available'])->toBe(14)
        ->and($credits->summaryFor($user)['reserved'])->toBe(0);

    $secondReservation = $credits->reserve($user, 5);
    $credits->release($secondReservation);

    expect($credits->summaryFor($user)['available'])->toBe(14)
        ->and($credits->summaryFor($user)['reserved'])->toBe(0);
});

it('grants payment credits only once for repeated success events', function () {
    $user = User::factory()->create();
    $payment = Payment::create([
        'uuid' => (string) Str::uuid(),
        'user_type' => User::class,
        'user_id' => $user->id,
        'gateway' => 'log',
        'gateway_payment_id' => 'log-test',
        'amount' => 12,
        'currency' => 'USD',
        'status' => 'completed',
        'payment_method' => 'card',
        'description' => '100 credit pack',
        'metadata' => [
            'credits_module' => true,
            'credits_reason' => 'credit_pack_purchase',
            'credits' => 100,
            'credit_pack_name' => 'Starter top-up',
        ],
        'paid_at' => now(),
    ]);

    $listener = app(GrantCreditsForSuccessfulPayment::class);
    $listener->handle(new PaymentSucceeded($payment));
    $listener->handle(new PaymentSucceeded($payment));

    expect(app(CreditService::class)->summaryFor($user)['available'])->toBe(100)
        ->and(CreditTransaction::query()->where('reason', 'credit_pack_purchase')->count())->toBe(1);
});
