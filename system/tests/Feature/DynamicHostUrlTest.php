<?php

use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

it('resolves the base url from the live request host, not APP_URL', function () {
    config(['app.url' => 'http://localhost']);

    $this->get('http://custom-domain.test/login');

    expect(real_host())->toBe('http://custom-domain.test');
});

it('forces route generation to use the live request host via middleware', function () {
    $response = $this->get('http://tenant.example.com/login');

    $response->assertOk();
    expect(URL::route('login'))->toStartWith('http://tenant.example.com');
});

it('caches the real host so off-request contexts can reuse it', function () {
    Cache::forget('app.real_host');

    $this->get('http://queued-host.test/login');

    expect(Cache::get('app.real_host'))->toBe('http://queued-host.test');
});

it('keeps the subfolder in generated urls when installed in a subdirectory', function () {
    $response = $this->withServerVariables([
        'SCRIPT_NAME' => '/softivus-dashboard/index.php',
        'PHP_SELF' => '/softivus-dashboard/index.php',
        'SCRIPT_FILENAME' => '/var/www/softivus-dashboard/index.php',
    ])->get('http://localhost/softivus-dashboard/login');

    $response->assertOk();

    // Routes, assets and uploads must all carry the /softivus-dashboard prefix,
    // otherwise css/js/images 404 on subfolder installs.
    expect(URL::route('login'))->toStartWith('http://localhost/softivus-dashboard')
        ->and(asset('assets/build/app.css'))->toBe('http://localhost/softivus-dashboard/assets/build/app.css')
        ->and(Storage::disk('public')->url('brand/logo.png'))->toBe('/softivus-dashboard/assets/uploads/brand/logo.png');

    // The page's own asset tags carry the prefix too.
    expect($response->getContent())->toContain('/softivus-dashboard/assets/');

    // Admin auth layout's illustration follows the dynamic root as well.
    $adminLogin = $this->withServerVariables([
        'SCRIPT_NAME' => '/softivus-dashboard/index.php',
        'PHP_SELF' => '/softivus-dashboard/index.php',
        'SCRIPT_FILENAME' => '/var/www/softivus-dashboard/index.php',
    ])->get('http://localhost/softivus-dashboard/admin/login');

    $adminLogin->assertOk();
    expect($adminLogin->getContent())
        ->toContain('/softivus-dashboard/assets/admin/images/admin-auth-illustration.svg');
});

it('renders queued mail links with the request host captured at construction', function () {
    $user = User::factory()->create();

    Cache::forever('app.real_host', 'https://live-site.com');

    $mail = new WelcomeMail($user);

    $rendered = $mail->render();

    expect($rendered)->toContain('https://live-site.com')
        ->and($rendered)->not->toContain('http://localhost/dashboard');
});
