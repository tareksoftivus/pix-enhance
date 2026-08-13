<?php

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('sends a signed-in admin from the admin login page to the admin dashboard', function (): void {
    $admin = Admin::create([
        'name' => 'Redirect Admin',
        'email' => 'redirect-admin@example.com',
        'password' => 'password',
        'is_active' => true,
    ]);

    $this->actingAs($admin, 'admin')
        ->get(route('admin.login'))
        ->assertRedirect(route('admin.dashboard'));
});

it('sends a signed-in user from the login page to the user dashboard', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user, 'web')
        ->get(route('login'))
        ->assertRedirect(route('user.dashboard'));
});
