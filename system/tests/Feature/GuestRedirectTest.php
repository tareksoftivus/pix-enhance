<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirects unauthenticated admin-panel requests to the admin login', function () {
    $this->get(route('admin.dashboard'))
        ->assertRedirect(route('admin.login'));
});

it('redirects unauthenticated user-panel requests to the user login', function () {
    $this->get(route('user.dashboard'))
        ->assertRedirect(route('login'));
});

it('does not send admin guests to the user login', function () {
    $this->get(route('admin.dashboard'))
        ->assertRedirect(route('admin.login'));

    expect(route('admin.login'))->not->toBe(route('login'));
});
