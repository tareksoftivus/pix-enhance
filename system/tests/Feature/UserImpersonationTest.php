<?php

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

function impersonatingAdmin(): Admin
{
    $role = Role::findOrCreate('super-admin', 'admin');

    $admin = Admin::query()->create([
        'name' => 'Impersonator',
        'email' => 'impersonator@example.com',
        'password' => 'password',
        'is_active' => true,
        'email_verified_at' => now(),
    ]);

    return tap($admin)->assignRole($role);
}

it('starts impersonation and returns the user dashboard url', function () {
    $admin = impersonatingAdmin();
    $user = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);

    $response = $this->actingAs($admin, 'admin')
        ->getJson(route('admin.users.impersonate', $user));

    $response->assertSuccessful()
        ->assertJson(['url' => route('user.dashboard')]);

    // The target user is now authenticated on the web guard.
    expect(auth('web')->id())->toBe($user->id)
        ->and(auth('admin')->id())->toBe($admin->id);
});

it('gates impersonation behind the users.edit permission', function () {
    Permission::findOrCreate('users.edit', 'admin');

    // An admin without super-admin and without users.edit.
    $admin = Admin::query()->create([
        'name' => 'Weak Admin',
        'email' => 'weak@example.com',
        'password' => 'password',
        'is_active' => true,
        'email_verified_at' => now(),
    ]);
    $user = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);

    $this->actingAs($admin, 'admin')
        ->getJson(route('admin.users.impersonate', $user))
        ->assertForbidden();

    expect(auth('web')->check())->toBeFalse();
});

it('lets the impersonated user reach the user dashboard', function () {
    $admin = impersonatingAdmin();
    $user = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);

    $this->actingAs($admin, 'admin')
        ->getJson(route('admin.users.impersonate', $user))
        ->assertSuccessful();

    $this->get(route('user.dashboard'))->assertSuccessful();
});

it('shows the impersonation banner on the user panel but not the admin panel', function () {
    $admin = impersonatingAdmin();
    $user = User::factory()->create(['name' => 'Target User', 'is_active' => true, 'email_verified_at' => now()]);

    $this->actingAs($admin, 'admin')
        ->getJson(route('admin.users.impersonate', $user))
        ->assertSuccessful();

    // Banner is present on the user panel (where the impersonated user is).
    $this->get(route('user.dashboard'))
        ->assertSuccessful()
        ->assertSee('Return to Admin');

    // Banner is absent on the admin panel (the admin is still their real self).
    $this->get(route('admin.dashboard'))
        ->assertSuccessful()
        ->assertDontSee('Return to Admin');
});

it('stops impersonation, logging the user out and keeping the admin', function () {
    $admin = impersonatingAdmin();
    $user = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);

    $this->actingAs($admin, 'admin')
        ->getJson(route('admin.users.impersonate', $user))
        ->assertSuccessful();

    expect(auth('web')->id())->toBe($user->id);

    $this->post(route('admin.impersonation.stop'))
        ->assertRedirect(route('admin.dashboard'));

    expect(auth('web')->check())->toBeFalse()
        ->and(auth('admin')->id())->toBe($admin->id);
});

it('records the impersonation in the login activity log', function () {
    $admin = impersonatingAdmin();
    $user = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);

    $this->actingAs($admin, 'admin')
        ->getJson(route('admin.users.impersonate', $user))
        ->assertSuccessful();

    $this->assertDatabaseHas('login_activities', [
        'event' => 'impersonate_start',
        'user_id' => $admin->id,
    ]);
});
