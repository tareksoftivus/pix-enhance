<?php

use App\Models\Admin;
use App\Modules\Currencies\Models\Currency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

function actingAsSuperAdmin(): Admin
{
    Role::findOrCreate('super-admin', 'admin');

    $admin = Admin::create([
        'name' => 'Form Admin',
        'email' => 'form-admin@example.com',
        'password' => 'password',
        'is_active' => true,
    ]);

    $admin->assignRole('super-admin');

    test()->actingAs($admin, 'admin');

    return $admin;
}

it('renders the edit currency form without inline css and with an uppercase code field', function () {
    actingAsSuperAdmin();

    $currency = Currency::create([
        'code' => 'SGD',
        'name' => 'Singapore Dollar',
        'symbol' => 'S$',
        'exchange_rate' => 1.35,
        'is_active' => true,
        'sort_order' => 15,
    ]);

    $response = $this->get(route('admin.currencies.edit', $currency));

    $response->assertSuccessful();
    $response->assertDontSee('text-transform', false);
    $response->assertSee('Rate relative to the base currency.');

    // The Code input carries the uppercase utility class (not an inline style).
    expect($response->getContent())->toMatch('/<input[^>]*name="code"[^>]*class="[^"]*\buppercase\b/');
});

it('keeps the Singapore Dollar symbol intact as S$ (no duplication)', function () {
    actingAsSuperAdmin();

    $currency = Currency::create([
        'code' => 'SGD',
        'name' => 'Singapore Dollar',
        'symbol' => 'S$',
        'exchange_rate' => 1.35,
        'is_active' => true,
        'sort_order' => 15,
    ]);

    $response = $this->get(route('admin.currencies.edit', $currency));

    // The symbol field value is exactly S$, never doubled to S$$ or $S$.
    expect($response->getContent())
        ->toContain('value="S$"')
        ->not->toContain('S$$');
});

it('renders the create currency form cleanly', function () {
    actingAsSuperAdmin();

    $response = $this->get(route('admin.currencies.create'));

    $response->assertSuccessful();
    $response->assertDontSee('text-transform', false);
    $response->assertSee('ISO 4217 three-letter code.');
});
