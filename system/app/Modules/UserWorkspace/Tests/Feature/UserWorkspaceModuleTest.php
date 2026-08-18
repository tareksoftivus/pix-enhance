<?php

use App\Models\User;
use App\Modules\LoginActivity\Models\LoginActivity;
use App\Modules\PaymentGateways\Models\Payment;
use App\Modules\Shared\Support\ModuleRegistry;
use App\Modules\Support\Models\SupportTicket;
use App\Modules\UserWorkspace\Models\UserWorkspacePreference;
use App\Modules\UserWorkspace\Services\UserWorkspaceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('registers the user workspace module and user preference routes', function () {
    $module = app(ModuleRegistry::class)->find('user-workspace');

    expect($module)->not->toBeNull()
        ->and($module['descriptor'])->not->toBeNull()
        ->and(Route::has('user.workspace.notifications.update'))->toBeTrue()
        ->and(Route::has('user.workspace.render-defaults.update'))->toBeTrue();
});

it('creates default preferences for a user workspace', function () {
    $user = User::factory()->create();

    $preferences = app(UserWorkspaceService::class)->preferencesFor($user);

    expect($preferences['notifications']['render_finished'])->toBeTrue()
        ->and($preferences['notifications']['weekly_summary'])->toBeFalse()
        ->and($preferences['render_defaults']['default_model'])->toBe('auto')
        ->and($preferences['render_defaults']['default_scale'])->toBe(4)
        ->and($preferences['source_retention_days'])->toBe(7);
});

it('updates notification preferences for the signed in user', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user)
        ->put(route('user.workspace.notifications.update'), [
            'render_finished' => '0',
            'credits_low' => '1',
            'weekly_summary' => '1',
            'product_news' => '0',
            'desktop_notifications_enabled' => '1',
            'completion_sound_enabled' => '1',
        ])
        ->assertRedirect();

    $preference = UserWorkspacePreference::query()->where('user_id', $user->id)->firstOrFail();

    expect($preference->notification_preferences)->toMatchArray([
        'render_finished' => false,
        'credits_low' => true,
        'weekly_summary' => true,
        'product_news' => false,
    ])->and($preference->desktop_notifications_enabled)->toBeTrue()
        ->and($preference->completion_sound_enabled)->toBeTrue();
});

it('updates render defaults for the signed in user', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user)
        ->put(route('user.workspace.render-defaults.update'), [
            'default_model' => 'photo-real',
            'default_scale' => 8,
            'default_format' => 'webp',
            'face_restoration' => '0',
            'auto_download' => '1',
            'source_retention_days' => 30,
        ])
        ->assertRedirect();

    $preference = UserWorkspacePreference::query()->where('user_id', $user->id)->firstOrFail();

    expect($preference->render_defaults)->toMatchArray([
        'default_model' => 'photo-real',
        'default_scale' => 8,
        'default_format' => 'webp',
        'face_restoration' => false,
        'auto_download' => true,
    ])->and($preference->source_retention_days)->toBe(30);
});

it('rejects unsupported render defaults', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user)
        ->from(route('user.settings'))
        ->put(route('user.workspace.render-defaults.update'), [
            'default_model' => 'unknown-model',
            'default_scale' => 16,
            'default_format' => 'gif',
            'source_retention_days' => 365,
        ])
        ->assertRedirect(route('user.settings'))
        ->assertSessionHasErrors(['default_model', 'default_scale', 'default_format', 'source_retention_days']);
});

it('builds user history from owned account, support, billing and security activity', function () {
    $user = User::factory()->create([
        'email' => 'owner@example.com',
        'email_verified_at' => now()->subDays(5),
        'created_at' => now()->subDays(7),
        'updated_at' => now()->subDays(7),
    ]);
    $other = User::factory()->create();

    SupportTicket::create([
        'reference' => 'TKT-123456',
        'user_id' => $user->id,
        'subject' => 'Billing question',
        'body' => 'Please help.',
        'priority' => 'medium',
        'status' => 'open',
        'last_reply_at' => now()->subDay(),
        'created_at' => now()->subDay(),
        'updated_at' => now()->subDay(),
    ]);

    SupportTicket::create([
        'reference' => 'TKT-654321',
        'user_id' => $other->id,
        'subject' => 'Other customer issue',
        'body' => 'Private.',
        'priority' => 'medium',
        'status' => 'open',
        'last_reply_at' => now()->subDay(),
        'created_at' => now()->subDay(),
        'updated_at' => now()->subDay(),
    ]);

    Payment::create([
        'uuid' => (string) Str::uuid(),
        'user_type' => User::class,
        'user_id' => $user->id,
        'gateway' => 'stripe',
        'amount' => 29,
        'currency' => 'USD',
        'status' => 'completed',
        'payment_method' => 'card',
        'description' => 'Studio plan',
        'paid_at' => now()->subHours(8),
    ]);

    Payment::create([
        'uuid' => (string) Str::uuid(),
        'user_type' => User::class,
        'user_id' => $other->id,
        'gateway' => 'stripe',
        'amount' => 99,
        'currency' => 'USD',
        'status' => 'completed',
        'payment_method' => 'card',
        'description' => 'Other customer plan',
        'paid_at' => now()->subHours(8),
    ]);

    LoginActivity::create([
        'user_type' => User::class,
        'user_id' => $user->id,
        'event' => 'login',
        'ip_address' => '127.0.0.1',
        'device' => 'Desktop',
        'browser' => 'Chrome',
        'platform' => 'Linux',
        'created_at' => now()->subMinutes(30),
    ]);

    $history = app(UserWorkspaceService::class)->historyFor($user, [], 50);
    $titles = collect($history['events']->items())->pluck('title')->implode(' ');
    $details = collect($history['events']->items())->pluck('detail')->implode(' ');

    expect($history['stats']['support'])->toBe(1)
        ->and($history['stats']['billing'])->toBe(1)
        ->and($history['stats']['security'])->toBe(1)
        ->and($titles)->toContain('Opened ticket TKT-123456')
        ->and($titles)->toContain('Payment completed')
        ->and($titles)->toContain('Signed in')
        ->and($details)->toContain('Billing question')
        ->and($details)->not->toContain('Other customer');
});

it('filters history by type and search term', function () {
    $user = User::factory()->create();

    Payment::create([
        'uuid' => (string) Str::uuid(),
        'user_type' => User::class,
        'user_id' => $user->id,
        'gateway' => 'stripe',
        'amount' => 49,
        'currency' => 'USD',
        'status' => 'completed',
        'payment_method' => 'card',
        'description' => 'Annual credit pack',
        'paid_at' => now()->subHour(),
    ]);

    SupportTicket::create([
        'reference' => 'TKT-111111',
        'user_id' => $user->id,
        'subject' => 'Annual receipt question',
        'body' => 'Please help.',
        'priority' => 'medium',
        'status' => 'open',
        'last_reply_at' => now()->subMinutes(20),
    ]);

    $history = app(UserWorkspaceService::class)->historyFor($user, [
        'type' => 'billing',
        'search' => 'annual',
    ], 50);

    $events = collect($history['events']->items());

    expect($history['filters']['type'])->toBe('billing')
        ->and($history['counts']['billing'])->toBe(1)
        ->and($history['counts']['support'])->toBe(1)
        ->and($events)->toHaveCount(1)
        ->and($events->first()['type'])->toBe('billing')
        ->and($events->first()['detail'])->toBe('Annual credit pack');
});
