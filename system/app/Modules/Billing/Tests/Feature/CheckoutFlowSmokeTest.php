<?php

use App\Models\User;
use App\Modules\Credits\Services\CreditService;
use App\Modules\PricingPlan\Models\PricingPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('redirects to billing when no order is selected', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user)
        ->get(route('user.checkout'))
        ->assertRedirect(route('user.billing'));
});

it('shows a real credit pack order on the checkout page', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user)
        ->get(route('user.checkout', ['type' => 'pack', 'pack' => 'starter']))
        ->assertOk()
        ->assertSee('Starter top-up')
        ->assertSee(route('user.credits.packs.purchase'), false);
});

it('shows a real pricing plan order on the checkout page', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $plan = PricingPlan::create([
        'name' => 'Growth',
        'slug' => 'growth',
        'tagline' => 'For growing teams',
        'price_monthly' => 29,
        'price_yearly' => 290,
        'credits_monthly' => 500,
        'features' => ['Priority support'],
        'cta_label' => 'Choose Growth',
        'is_active' => true,
        'is_featured' => false,
        'sort_order' => 1,
    ]);

    $this->actingAs($user)
        ->get(route('user.checkout', ['type' => 'plan', 'plan' => $plan->id]))
        ->assertOk()
        ->assertSee('Growth')
        ->assertSee(route('user.credits.plans.purchase', $plan), false);
});

it('completes a real checkout purchase for a credit pack via the log gateway', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user)
        ->post(route('user.credits.packs.purchase'), ['pack' => 'starter'])
        ->assertRedirect();

    expect(app(CreditService::class)->summaryFor($user)['available'])->toBeGreaterThan(0);
});

it('links from billing to checkout for packs and plans', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $plan = PricingPlan::create([
        'name' => 'Growth',
        'slug' => 'growth',
        'tagline' => 'For growing teams',
        'price_monthly' => 29,
        'price_yearly' => 290,
        'credits_monthly' => 500,
        'features' => [],
        'cta_label' => 'Choose Growth',
        'is_active' => true,
        'is_featured' => false,
        'sort_order' => 1,
    ]);

    $this->actingAs($user)
        ->get(route('user.billing'))
        ->assertOk()
        ->assertSee(route('user.checkout', ['type' => 'pack', 'pack' => 'starter']))
        ->assertSee(route('user.checkout', ['type' => 'plan', 'plan' => $plan->id]));
});
