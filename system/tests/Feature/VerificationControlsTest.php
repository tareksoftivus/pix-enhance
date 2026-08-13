<?php

use App\Models\User;
use App\Modules\Settings\Services\SettingsService;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

function registerPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Test User',
        'email' => 'new-user@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ], $overrides);
}

it('sends the verification email at registration', function () {
    Notification::fake();

    $this->post(route('register'), registerPayload());

    $user = User::where('email', 'new-user@example.com')->firstOrFail();

    Notification::assertSentTo($user, VerifyEmail::class);
});

it('resets phone verification when the user changes their phone number', function () {
    $settings = app(SettingsService::class);
    $settings->set('require_email_verification', false);
    $settings->set('require_sms_verification', true);

    $user = User::factory()->create([
        'email_verified_at' => now(),
        'is_active' => true,
        'phone' => '+14155550100',
        'phone_verified_at' => now(),
    ]);

    $this->actingAs($user)->put(route('user.profile.update'), [
        'name' => $user->name,
        'email' => $user->email,
        'phone' => '+14155559999',
    ])->assertSessionHasNoErrors();

    expect($user->fresh()->phone_verified_at)->toBeNull();

    // The new, unverified number re-gates the dashboard.
    $this->get(route('user.dashboard'))->assertRedirect(route('user.phone.verification.notice'));
});

it('resets email verification and resends the link when the user changes their email', function () {
    Notification::fake();

    $user = User::factory()->create([
        'email_verified_at' => now(),
        'is_active' => true,
    ]);

    $this->actingAs($user)->put(route('user.profile.update'), [
        'name' => $user->name,
        'email' => 'brand-new@example.com',
        'phone' => $user->phone,
    ])->assertSessionHasNoErrors();

    $fresh = $user->fresh();

    expect($fresh->email_verified_at)->toBeNull()
        ->and($fresh->email)->toBe('brand-new@example.com');

    Notification::assertSentTo($fresh, VerifyEmail::class);
});

it('requires email verification by default', function () {
    $this->post(route('register'), registerPayload());

    $user = User::where('email', 'new-user@example.com')->firstOrFail();

    expect($user->email_verified_at)->toBeNull();

    // Unverified users are bounced from the dashboard to the notice page.
    $this->get(route('user.dashboard'))->assertRedirect(route('verification.notice'));
});

it('skips email verification when the control is off', function () {
    app(SettingsService::class)->set('require_email_verification', false);

    $this->post(route('register'), registerPayload());

    $user = User::where('email', 'new-user@example.com')->firstOrFail();

    expect($user->email_verified_at)->not->toBeNull();

    $this->get(route('user.dashboard'))->assertSuccessful();
});

it('lets existing unverified users in when email verification is turned off', function () {
    $user = User::factory()->create(['email_verified_at' => null, 'is_active' => true]);

    app(SettingsService::class)->set('require_email_verification', false);

    $this->actingAs($user)->get(route('user.dashboard'))->assertSuccessful();
});

it('requires a phone number at registration when sms verification is on', function () {
    app(SettingsService::class)->set('require_sms_verification', true);

    $response = $this->post(route('register'), registerPayload());

    $response->assertSessionHasErrors('phone');
});

it('routes new registrations through phone verification when sms verification is on', function () {
    $settings = app(SettingsService::class);
    $settings->set('require_email_verification', false);
    $settings->set('require_sms_verification', true);

    $response = $this->post(route('register'), registerPayload(['phone' => '+14155550100']));

    $response->assertRedirect(route('user.phone.verification.notice'));

    // The dashboard stays gated until the code is confirmed.
    $this->get(route('user.dashboard'))->assertRedirect(route('user.phone.verification.notice'));

    // "123456" is the OtpDeliveryService dev bypass outside production.
    $this->post(route('user.phone.verification.verify'), ['otp' => '123456'])
        ->assertRedirect(route('user.dashboard'));

    $user = User::where('email', 'new-user@example.com')->firstOrFail();

    expect($user->phone_verified_at)->not->toBeNull();

    $this->get(route('user.dashboard'))->assertSuccessful();
});

it('lets grandfathered users add and verify a phone when sms verification is turned on', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'is_active' => true,
        'phone' => null,
    ]);

    app(SettingsService::class)->set('require_sms_verification', true);

    $this->actingAs($user);

    $this->get(route('user.dashboard'))->assertRedirect(route('user.phone.verification.notice'));
    $this->get(route('user.phone.verification.notice'))->assertSuccessful();

    $this->post(route('user.phone.verification.send'), ['phone' => '+14155550111'])
        ->assertSessionHasNoErrors();

    $this->post(route('user.phone.verification.verify'), ['otp' => '123456'])
        ->assertRedirect(route('user.dashboard'));

    expect($user->fresh()->phone_verified_at)->not->toBeNull();
});

it('does not gate the dashboard on phone verification when sms verification is off', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'is_active' => true,
        'phone' => null,
    ]);

    $this->actingAs($user)->get(route('user.dashboard'))->assertSuccessful();
});

it('does not show the phone field at registration when sms verification is off', function () {
    $this->get(route('register'))->assertDontSee('name="phone"', false);
});

it('keeps the phone verification screen reachable while email is still unverified', function () {
    $settings = app(SettingsService::class);
    $settings->set('require_email_verification', true);
    $settings->set('require_sms_verification', true);

    $this->post(route('register'), registerPayload(['phone' => '+14155550100']))
        ->assertRedirect(route('user.phone.verification.notice'));

    // Both gates pending: the phone screen must not bounce to the email notice.
    $this->get(route('user.phone.verification.notice'))->assertSuccessful();
});

it('completes registration even when the sms gateway is misconfigured', function () {
    $settings = app(SettingsService::class);
    $settings->set('require_email_verification', false);
    $settings->set('require_sms_verification', true);
    $settings->set('sms_provider', 'twilio');

    $response = $this->post(route('register'), registerPayload(['phone' => '+14155550100']));

    // Twilio has no credentials — sign-up still succeeds and lands on the
    // verification screen instead of a 500.
    $response->assertRedirect(route('user.phone.verification.notice'));

    expect(User::where('email', 'new-user@example.com')->exists())->toBeTrue();
});
