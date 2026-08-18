<?php

use App\Models\User;
use App\Modules\Credits\Exceptions\InsufficientCreditsException;
use App\Modules\Credits\Models\CreditReservation;
use App\Modules\Credits\Models\CreditTransaction;
use App\Modules\Credits\Services\CreditService;
use App\Modules\RenderJobs\Models\RenderJob;
use App\Modules\RenderJobs\Services\RenderJobService;
use App\Modules\Shared\Support\ModuleRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
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
