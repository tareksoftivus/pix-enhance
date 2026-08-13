<?php

use App\Modules\Blog\Models\BlogPost;
use App\Modules\Settings\Providers\SettingsServiceProvider;
use App\Modules\Settings\Services\SettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

/**
 * Re-run the provider's storage override the way boot() does, after the
 * test has written its storage settings to the database.
 */
function reapplyStorageSettings(): void
{
    $provider = new SettingsServiceProvider(app());
    $method = new ReflectionMethod($provider, 'applyStorageSettings');
    $method->invoke($provider);

    Storage::forgetDisk('public');
}

it('writes, reads and deletes files on the local public disk under assets/uploads', function () {
    Storage::disk('public')->put('probe/storage-test.txt', 'contents');

    expect(file_exists(public_path('assets/uploads/probe/storage-test.txt')))->toBeTrue()
        ->and(Storage::disk('public')->get('probe/storage-test.txt'))->toBe('contents');

    Storage::disk('public')->deleteDirectory('probe');

    expect(file_exists(public_path('assets/uploads/probe/storage-test.txt')))->toBeFalse();
});

it('generates public-disk urls for uploads instead of broken /storage paths', function () {
    expect(Storage::disk('public')->url('avatars/a.jpg'))->toBe('/assets/uploads/avatars/a.jpg');

    $post = new BlogPost(['cover_image' => 'blog/covers/x.jpg']);

    expect($post->coverImageUrl())->toBe('/assets/uploads/blog/covers/x.jpg')
        ->and($post->coverImageUrl())->not->toStartWith('/storage');
});

it('reconfigures the public disk to s3 from database settings', function () {
    $settings = app(SettingsService::class);
    $settings->set('storage_provider', 's3');
    $settings->set('storage_s3_key', 'test-key');
    $settings->set('storage_s3_secret', 'test-secret');
    $settings->set('storage_s3_region', 'eu-west-1');
    $settings->set('storage_s3_bucket', 'my-bucket');
    $settings->set('storage_s3_url', 'https://cdn.example.com');

    reapplyStorageSettings();

    $disk = config('filesystems.disks.public');

    expect($disk['driver'])->toBe('s3')
        ->and($disk['bucket'])->toBe('my-bucket')
        ->and($disk['region'])->toBe('eu-west-1')
        ->and($disk['use_path_style_endpoint'])->toBeFalse();

    // URL generation follows the bucket's public URL — used by avatars,
    // blog covers and media the moment a buyer switches providers.
    expect(Storage::disk('public')->url('avatars/a.jpg'))
        ->toBe('https://cdn.example.com/avatars/a.jpg');
});

it('reconfigures the public disk for cloudflare r2 with path-style addressing', function () {
    $settings = app(SettingsService::class);
    $settings->set('storage_provider', 'r2');
    $settings->set('storage_s3_key', 'r2-key');
    $settings->set('storage_s3_secret', 'r2-secret');
    $settings->set('storage_s3_bucket', 'r2-bucket');
    $settings->set('storage_s3_endpoint', 'https://account.r2.cloudflarestorage.com');

    reapplyStorageSettings();

    $disk = config('filesystems.disks.public');

    expect($disk['driver'])->toBe('s3')
        ->and($disk['endpoint'])->toBe('https://account.r2.cloudflarestorage.com')
        ->and($disk['use_path_style_endpoint'])->toBeTrue()
        ->and($disk['region'])->toBe('auto');
});

it('keeps the local public disk untouched when the provider is local', function () {
    app(SettingsService::class)->set('storage_provider', 'local');

    reapplyStorageSettings();

    $disk = config('filesystems.disks.public');

    expect($disk['driver'])->toBe('local')
        ->and($disk['url'])->toBe('/assets/uploads');
});
