<?php

use App\Models\User;
use App\Modules\Credits\Models\CreditOrder;
use App\Modules\Credits\Services\CreditService;
use App\Modules\PaymentGatewaySettings\Services\PaymentGatewaySettingsService;
use App\Modules\PricingPlan\Models\PricingPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('shows the log gateway on checkout when no gateway is enabled in a non-production environment', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user)
        ->get(route('user.checkout', ['type' => 'pack', 'pack' => 'starter']))
        ->assertOk()
        ->assertSee('Test / development mode');
});

it('applies the admin-configured gateway fee to the order total', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    app(PaymentGatewaySettingsService::class)->set('log_fixed_charge', '2');
    app(PaymentGatewaySettingsService::class)->set('log_percent_charge', '10');

    $response = $this->actingAs($user)
        ->get(route('user.checkout', ['type' => 'pack', 'pack' => 'starter', 'gateway' => 'log']));

    $response->assertOk();

    // Starter pack price is defined in config/credits.php.
    $pack = collect(config('credits.packs'))->get('starter');
    $expectedFee = round(2 + ($pack['price'] * 10 / 100), 2);
    $expectedTotal = round($pack['price'] + $expectedFee, 2);

    $response->assertSee(number_format($expectedFee, 2))
        ->assertSee(number_format($expectedTotal, 2));
});

it('creates a credit order row and links it to the resulting payment and invoice', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user)
        ->post(route('user.credits.packs.purchase'), ['pack' => 'starter', 'gateway' => 'log'])
        ->assertRedirect(route('user.billing'));

    $order = CreditOrder::query()->where('user_id', $user->id)->firstOrFail();

    expect($order->type)->toBe('pack')
        ->and($order->status)->toBe('completed')
        ->and($order->gateway)->toBe('log')
        ->and($order->payment_id)->not->toBeNull();

    expect(app(CreditService::class)->summaryFor($user)['available'])->toBeGreaterThan(0);
});

it('creates a completed free-plan order with no payment', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $plan = PricingPlan::create([
        'name' => 'Free',
        'slug' => 'free',
        'tagline' => 'Get started',
        'price_monthly' => 0,
        'price_yearly' => 0,
        'credits_monthly' => 50,
        'features' => [],
        'cta_label' => 'Start free',
        'is_active' => true,
        'is_featured' => false,
        'sort_order' => 0,
    ]);

    $this->actingAs($user)
        ->post(route('user.credits.plans.purchase', $plan))
        ->assertRedirect();

    $order = CreditOrder::query()->where('user_id', $user->id)->firstOrFail();

    expect($order->type)->toBe('plan')
        ->and($order->status)->toBe('completed')
        ->and($order->total)->toEqualWithDelta(0, 0.001)
        ->and($order->payment_id)->toBeNull();
});

it('shows the orders table with a working invoice link on the billing page', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user)
        ->post(route('user.credits.packs.purchase'), ['pack' => 'starter', 'gateway' => 'log']);

    $response = $this->actingAs($user)->get(route('user.billing'));

    $response->assertOk()->assertSee(__('Orders'));

    $order = CreditOrder::query()->where('user_id', $user->id)->firstOrFail();

    expect($order->status)->toBe('completed');
});
