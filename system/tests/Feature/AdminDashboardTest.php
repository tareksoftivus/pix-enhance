<?php

use App\Models\Admin;
use App\Models\User;
use App\Modules\Support\Models\SupportTicket;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the improved admin dashboard', function () {
    $admin = Admin::create([
        'name' => 'Dashboard Admin',
        'email' => 'admin@example.com',
        'password' => 'password',
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));

    $response->assertSuccessful();
    $response->assertSee('Dashboard');
    $response->assertSee('Revenue Overview');
    $response->assertSee('Payment Health');

    // Topbar carries a "Visit Site" link to the public frontend.
    $response->assertSee('Visit Site');
    $response->assertSee(route('home'));

    // Sidebar logo links to the admin dashboard and ships a dark-mode inverse
    // (packaged fallback applies when no custom logo is set).
    $response->assertSee('href="'.route('admin.dashboard').'"', false);
    $response->assertSee('softivus-logo-inverse.png');
    $response->assertSee('dark:hidden');
    $response->assertSee('dark:block');
});

it('renders the tabbed activity hub', function () {
    $admin = Admin::create([
        'name' => 'Dashboard Admin',
        'email' => 'admin@example.com',
        'password' => 'password',
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));

    $response->assertSuccessful();

    // One hub card with the three tab pills instead of three separate cards.
    $response->assertSee('Recent Activity');
    $response->assertSee("tab = 'payments'", false);
    $response->assertSee("tab = 'users'", false);
    $response->assertSee("tab = 'logins'", false);

    // The old standalone cards are gone, including the system card.
    $response->assertDontSee('Recent Payments');
    $response->assertDontSee('Login Activity');
    $response->assertDontSee('System Information');
});

it('renders the support snapshot with counts and latest tickets', function () {
    $admin = Admin::create([
        'name' => 'Dashboard Admin',
        'email' => 'admin@example.com',
        'password' => 'password',
        'is_active' => true,
    ]);

    $user = User::factory()->create();

    SupportTicket::create([
        'reference' => 'TKT-000001',
        'user_id' => $user->id,
        'subject' => 'Payment failed on renewal',
        'body' => 'My renewal payment keeps failing.',
        'category' => 'billing',
        'priority' => 'urgent',
        'status' => 'open',
    ]);

    $response = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));

    $response->assertSuccessful();
    $response->assertSee('Support Tickets');
    $response->assertSee('Payment failed on renewal');
    $response->assertSee('TKT-000001');
    $response->assertSee('Urgent');
});
