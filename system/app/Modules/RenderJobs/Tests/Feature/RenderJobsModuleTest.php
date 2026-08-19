<?php

use App\Models\User;
use App\Modules\AiSettings\Services\AiSettingsService;
use App\Modules\Credits\Exceptions\InsufficientCreditsException;
use App\Modules\Credits\Models\CreditReservation;
use App\Modules\Credits\Models\CreditTransaction;
use App\Modules\Credits\Services\CreditService;
use App\Modules\RenderJobs\Exceptions\AiRenderException;
use App\Modules\RenderJobs\Models\RenderJob;
use App\Modules\RenderJobs\Services\RenderJobService;
use App\Modules\Shared\Support\ModuleRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Laravel\Ai\Exceptions\AiException;
use Laravel\Ai\Image;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('registers the render jobs module and routes', function () {
    $module = app(ModuleRegistry::class)->find('render-jobs');

    expect($module)->not->toBeNull()
        ->and($module['descriptor'])->not->toBeNull()
        ->and(Route::has('admin.render-jobs.index'))->toBeTrue()
        ->and(Route::has('user.render-jobs.store'))->toBeTrue()
        ->and(Route::has('user.render-jobs.download'))->toBeTrue();
});

it('creates a completed render job and captures credits', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    app(CreditService::class)->grant($user, 10, 'test_grant');

    $job = app(RenderJobService::class)->create($user, UploadedFile::fake()->image('portrait.png', 120, 80), [
        'tool' => 'upscaler',
        'scale' => 2,
        'output_format' => 'png',
        'model' => 'enhance-xl',
    ]);

    expect($job->status)->toBe('completed')
        ->and($job->credits_cost)->toBe(1)
        ->and(app(CreditService::class)->summaryFor($user)['available'])->toBe(9)
        ->and(CreditReservation::query()->where('reservable_id', $job->id)->where('status', 'captured')->exists())->toBeTrue()
        ->and(CreditTransaction::query()->where('reference_id', $job->id)->where('reason', 'render_spend')->exists())->toBeTrue();

    Storage::disk('public')->assertExists($job->source_path);
    Storage::disk('public')->assertExists($job->output_path);
});

it('prevents render jobs when credits are unavailable', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    expect(fn () => app(RenderJobService::class)->create($user, UploadedFile::fake()->image('portrait.png'), [
        'tool' => 'upscaler',
        'scale' => 4,
        'output_format' => 'png',
    ]))->toThrow(InsufficientCreditsException::class);

    expect(RenderJob::query()->count())->toBe(0);
});

it('does not allow users to download another users render', function () {
    Storage::fake('public');

    $owner = User::factory()->create();
    $viewer = User::factory()->create();
    app(CreditService::class)->grant($owner, 10, 'test_grant');

    $job = app(RenderJobService::class)->create($owner, UploadedFile::fake()->image('portrait.png'), [
        'tool' => 'background-removal',
        'scale' => 1,
        'output_format' => 'png',
    ]);

    $this->actingAs($viewer)
        ->get(route('user.render-jobs.download', $job))
        ->assertNotFound();
});

it('clears finished render history but keeps jobs in progress', function () {
    $user = User::factory()->create();

    $completed = RenderJob::factory()->create(['user_id' => $user->id, 'status' => 'completed']);
    $failed = RenderJob::factory()->create(['user_id' => $user->id, 'status' => 'failed']);
    $cancelled = RenderJob::factory()->create(['user_id' => $user->id, 'status' => 'cancelled']);
    $queued = RenderJob::factory()->create(['user_id' => $user->id, 'status' => 'queued']);
    $othersJob = RenderJob::factory()->create(['status' => 'completed']);

    $cleared = app(RenderJobService::class)->clearHistoryForUser($user);

    expect($cleared)->toBe(3)
        ->and(RenderJob::query()->whereKey([$completed->id, $failed->id, $cancelled->id])->count())->toBe(0)
        ->and(RenderJob::query()->whereKey($queued->id)->exists())->toBeTrue()
        ->and(RenderJob::query()->whereKey($othersJob->id)->exists())->toBeTrue();
});

it('clears render history over http for the signed in user only', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    RenderJob::factory()->create(['user_id' => $user->id, 'status' => 'completed']);
    RenderJob::factory()->create(['user_id' => $user->id, 'status' => 'queued']);

    $this->actingAs($user)
        ->delete(route('user.render-jobs.clear-history'))
        ->assertRedirect();

    expect(RenderJob::query()->where('user_id', $user->id)->count())->toBe(1)
        ->and(RenderJob::query()->where('user_id', $user->id)->first()->status)->toBe('queued');
});

it('routes an enabled ai model through the ai processor', function () {
    Storage::fake('public');
    Image::fake(); // defaults to a fake base64 image payload per attempt

    $user = User::factory()->create();
    app(CreditService::class)->grant($user, 10, 'test_grant');

    $aiSettings = app(AiSettingsService::class);
    $aiSettings->set('gemini_enabled', true);
    $aiSettings->set('gemini_api_key', 'test-key');
    $aiSettings->set('gemini_image_models', 'gemini-2.5-flash-image');

    $job = app(RenderJobService::class)->create($user, UploadedFile::fake()->image('portrait.png', 120, 80), [
        'tool' => 'upscaler',
        'scale' => 2,
        'output_format' => 'png',
        'model' => 'gemini:gemini-2.5-flash-image',
    ]);

    expect($job->status)->toBe('completed');

    Image::assertGenerated(fn ($prompt) => $prompt->contains('Upscaler'));
    Storage::disk('public')->assertExists($job->output_path);
});

it('falls back to the local gd processor when no ai model is selected', function () {
    Storage::fake('public');
    Image::fake();

    $user = User::factory()->create();
    app(CreditService::class)->grant($user, 10, 'test_grant');

    $job = app(RenderJobService::class)->create($user, UploadedFile::fake()->image('portrait.png', 120, 80), [
        'tool' => 'upscaler',
        'scale' => 2,
        'output_format' => 'png',
        'model' => 'auto',
    ]);

    expect($job->status)->toBe('completed');

    Image::assertNothingGenerated();
});

it('marks the job failed without leaking raw exception detail when the ai call throws', function () {
    Storage::fake('public');
    Image::fake(function () {
        throw new AiException('Upstream said: sk-secret-key-12345 is invalid');
    });

    $user = User::factory()->create();
    app(CreditService::class)->grant($user, 10, 'test_grant');

    $aiSettings = app(AiSettingsService::class);
    $aiSettings->set('gemini_enabled', true);
    $aiSettings->set('gemini_api_key', 'test-key');
    $aiSettings->set('gemini_image_models', 'gemini-2.5-flash-image');

    expect(fn () => app(RenderJobService::class)->create($user, UploadedFile::fake()->image('portrait.png', 120, 80), [
        'tool' => 'upscaler',
        'scale' => 2,
        'output_format' => 'png',
        'model' => 'gemini:gemini-2.5-flash-image',
    ]))->toThrow(AiRenderException::class);

    $job = RenderJob::query()->where('user_id', $user->id)->firstOrFail();

    expect($job->status)->toBe('failed')
        ->and($job->error_message)->not->toContain('sk-secret-key-12345')
        ->and(CreditReservation::query()->where('reservable_id', $job->id)->where('status', 'released')->exists())->toBeTrue();
});
