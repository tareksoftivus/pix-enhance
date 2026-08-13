<?php

use App\Modules\Settings\Services\SettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('serves plain http normally while force https is off', function () {
    $this->get('http://example.test/login')->assertSuccessful();
});

it('redirects http traffic to https when force https is on', function () {
    app(SettingsService::class)->set('force_https', true);

    $this->get('http://example.test/login')
        ->assertMovedPermanently()
        ->assertRedirect('https://example.test/login');
});

it('lets https traffic through when force https is on', function () {
    app(SettingsService::class)->set('force_https', true);

    $this->get('https://example.test/login')->assertSuccessful();
});

it('generates https urls while force https is on', function () {
    app(SettingsService::class)->set('force_https', true);

    $this->get('https://example.test/login');

    expect(route('login'))->toStartWith('https://');
});
