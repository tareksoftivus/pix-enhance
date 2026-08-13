<?php

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

function demoAdmin(): Admin
{
    Role::findOrCreate('super-admin', 'admin');
    $admin = Admin::create([
        'name' => 'Demo Admin',
        'email' => 'demo-admin@example.com',
        'password' => 'password',
        'is_active' => true,
    ]);
    $admin->assignRole('super-admin');

    return $admin;
}

it('allows admin writes when demo mode is off', function () {
    config(['app.demo_mode' => false]);

    $this->actingAs(demoAdmin(), 'admin')
        ->post('/admin/users', ['name' => 'Test'])
        ->assertSessionMissing('error');
});

it('blocks admin writes when demo mode is on', function () {
    config(['app.demo_mode' => true]);

    $this->from('/admin/users')
        ->actingAs(demoAdmin(), 'admin')
        ->post('/admin/users', ['name' => 'Test'])
        ->assertRedirect('/admin/users')
        ->assertSessionHas('error');
});

it('still allows admin GET requests in demo mode', function () {
    config(['app.demo_mode' => true]);

    $this->actingAs(demoAdmin(), 'admin')->get('/admin/users')->assertSuccessful();
});

it('does not block admin login submit in demo mode', function () {
    config(['app.demo_mode' => true]);

    $admin = demoAdmin();
    $admin->forceFill(['password' => bcrypt('secret-pass')])->save();

    $this->post('/admin/login', ['email' => $admin->email, 'password' => 'secret-pass']);

    expect(session('error'))->toBeNull()
        ->and(auth()->guard('admin')->check())->toBeTrue();
});

it('does not block admin logout in demo mode', function () {
    config(['app.demo_mode' => true]);

    $this->from('/admin')->actingAs(demoAdmin(), 'admin')->post('/admin/logout');

    expect(session('error'))->toBeNull();
});

it('returns a json error for admin api writes in demo mode', function () {
    config(['app.demo_mode' => true]);

    $this->actingAs(demoAdmin(), 'admin')
        ->postJson('/admin/users', ['name' => 'Test'])
        ->assertForbidden()
        ->assertJsonStructure(['message']);
});

it('does not block user login in demo mode', function () {
    config(['app.demo_mode' => true]);

    User::factory()->create([
        'email' => 'demo-user@example.com',
        'password' => bcrypt('password'),
        'is_active' => true,
    ]);

    $this->post('/login', ['email' => 'demo-user@example.com', 'password' => 'password']);

    expect(session('error'))->toBeNull()
        ->and(auth()->guard('web')->check())->toBeTrue();
});
