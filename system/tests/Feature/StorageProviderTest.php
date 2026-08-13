<?php

use App\Modules\Settings\Providers\SettingsServiceProvider;
use App\Modules\Settings\Services\SettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Re-run the provider's storage override against the current DB settings.
 */
function rebootStorage(): void
{
    $provider = new SettingsServiceProvider(app());
    $method = new ReflectionMethod($provider, 'applyStorageSettings');
    $method->invoke($provider);
}

function setStorage(string $key, mixed $value): void
{
    app(SettingsService::class)->set($key, $value);
}

it('keeps the local disk pointing at assets/uploads by default', function () {
    setStorage('storage_provider', 'local');
    rebootStorage();

    $disk = config('filesystems.disks.public');

    expect($disk['driver'])->toBe('local')
        ->and($disk['root'])->toEndWith('assets/uploads');
});

it('reconfigures the public disk to s3 when provider is s3', function () {
    setStorage('storage_provider', 's3');
    setStorage('storage_s3_key', 'AKIA123');
    setStorage('storage_s3_secret', 'secret123');
    setStorage('storage_s3_region', 'us-east-1');
    setStorage('storage_s3_bucket', 'my-bucket');
    rebootStorage();

    $disk = config('filesystems.disks.public');

    expect($disk['driver'])->toBe('s3')
        ->and($disk['bucket'])->toBe('my-bucket')
        ->and($disk['region'])->toBe('us-east-1')
        ->and($disk['use_path_style_endpoint'])->toBeFalse();
});

it('uses the s3 driver with path-style + endpoint for cloudflare r2', function () {
    setStorage('storage_provider', 'r2');
    setStorage('storage_s3_bucket', 'r2-bucket');
    setStorage('storage_s3_endpoint', 'https://acct.r2.cloudflarestorage.com');
    setStorage('storage_s3_url', 'https://cdn.example.com');
    rebootStorage();

    $disk = config('filesystems.disks.public');

    expect($disk['driver'])->toBe('s3')
        ->and($disk['bucket'])->toBe('r2-bucket')
        ->and($disk['endpoint'])->toBe('https://acct.r2.cloudflarestorage.com')
        ->and($disk['url'])->toBe('https://cdn.example.com')
        ->and($disk['use_path_style_endpoint'])->toBeTrue();
});

it('defaults r2 region to auto when none is provided', function () {
    setStorage('storage_provider', 'r2');
    setStorage('storage_s3_region', '');
    rebootStorage();

    expect(config('filesystems.disks.public.region'))->toBe('auto');
});
