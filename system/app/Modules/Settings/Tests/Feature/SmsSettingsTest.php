<?php

use App\Models\Admin;
use App\Modules\Settings\Services\SettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

/**
 * Every defined setting with its default value — the settings form submits all
 * tabs together, so updates must always carry the full field set.
 *
 * @return array<string, mixed>
 */
function baseRequiredSettings(): array
{
    $fields = [];

    foreach (config('settings') as $group) {
        foreach ($group['settings'] ?? [] as $key => $definition) {
            $fields[$key] = $definition['default'] ?? '';
        }
    }

    return $fields;
}

function smsSettingsAdmin(): Admin
{
    Role::findOrCreate('super-admin', 'admin');

    $admin = Admin::create([
        'name' => 'Settings Admin',
        'email' => 'settings-admin@example.com',
        'password' => 'password',
        'is_active' => true,
    ]);

    $admin->assignRole('super-admin');

    test()->actingAs($admin, 'admin');

    return $admin;
}

it('shows the sms provider fields on the settings page', function () {
    smsSettingsAdmin();

    $response = $this->get(route('admin.settings.index'));

    $response->assertSuccessful();
    $response->assertSee('SMS Provider');
    $response->assertSee('Twilio Account SID');
    $response->assertSee('Vonage API Key');
});

it('saves twilio credentials through the settings form', function () {
    smsSettingsAdmin();

    $response = $this->put(route('admin.settings.update'), [
        'settings' => array_merge(baseRequiredSettings(), [
            'sms_provider' => 'twilio',
            'sms_from_number' => '+14155550100',
            'twilio_sid' => 'AC123',
            'twilio_auth_token' => 'secret-token',
        ]),
        '_active_tab' => 'notifications',
    ]);

    $response->assertSessionHasNoErrors();

    $settings = app(SettingsService::class);

    expect($settings->get('sms_provider'))->toBe('twilio')
        ->and($settings->get('sms_from_number'))->toBe('+14155550100')
        ->and($settings->get('twilio_sid'))->toBe('AC123')
        ->and($settings->get('twilio_auth_token'))->toBe('secret-token');
});

it('rejects an unknown sms provider', function () {
    smsSettingsAdmin();

    $response = $this->from(route('admin.settings.index'))->put(route('admin.settings.update'), [
        'settings' => array_merge(baseRequiredSettings(), [
            'sms_provider' => 'smsgateway.example',
        ]),
        '_active_tab' => 'notifications',
    ]);

    $response->assertSessionHasErrors(['settings.sms_provider']);
});

it('saves settings with default values without validation errors', function () {
    smsSettingsAdmin();

    // Regression: mailgun/smtp conditional fields previously lacked `nullable`,
    // so a plain save with empty provider credentials failed validation.
    $response = $this->put(route('admin.settings.update'), [
        'settings' => baseRequiredSettings(),
        '_active_tab' => 'general',
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect();
});

it('defaults the sms provider to the log driver', function () {
    expect(app(SettingsService::class)->get('sms_provider', 'log'))->toBe('log');
});
