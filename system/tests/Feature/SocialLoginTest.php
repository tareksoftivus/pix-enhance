<?php

use App\Models\User;
use App\Modules\Settings\Providers\SettingsServiceProvider;
use App\Modules\Settings\Services\SettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

uses(RefreshDatabase::class);

function setSetting(string $key, mixed $value): void
{
    app(SettingsService::class)->set($key, $value);
}

/**
 * Re-run the provider's social override so config('services.*') reflects settings.
 */
function rebootSocial(): void
{
    $provider = new SettingsServiceProvider(app());
    (new ReflectionMethod($provider, 'applySocialSettings'))->invoke($provider);
}

function enableGoogle(): void
{
    setSetting('social_google_enabled', true);
    setSetting('social_google_client_id', 'client-123');
    setSetting('social_google_client_secret', 'secret-123');
    rebootSocial();
}

function fakeSocialiteUser(string $id, string $email, string $name): void
{
    $socialiteUser = (new SocialiteUser)->map([
        'id' => $id,
        'name' => $name,
        'email' => $email,
        'avatar' => 'https://example.com/a.png',
    ]);

    $driver = Mockery::mock('Laravel\Socialite\Contracts\Provider');
    $driver->shouldReceive('user')->andReturn($socialiteUser);

    Socialite::shouldReceive('driver')->with('google')->andReturn($driver);
}

it('pushes enabled provider credentials into services config', function () {
    enableGoogle();

    expect(config('services.google.client_id'))->toBe('client-123')
        ->and(config('services.google.client_secret'))->toBe('secret-123')
        ->and(config('services.google.redirect'))->toBe(route('social.callback', 'google'));
});

it('does not configure a provider that is disabled', function () {
    setSetting('social_google_enabled', false);
    setSetting('social_google_client_id', 'should-not-apply');
    rebootSocial();

    expect(config('services.google.client_id'))->not->toBe('should-not-apply');
});

it('redirects the redirect route back to login when the provider is disabled', function () {
    $this->get(route('social.redirect', 'google'))
        ->assertRedirect(route('login'));
});

it('404s an unsupported provider', function () {
    enableGoogle();

    $this->get('/auth/twitter/redirect')->assertNotFound();
});

it('creates and logs in a new user from the provider callback', function () {
    enableGoogle();
    fakeSocialiteUser('gid-1', 'new@example.com', 'New User');

    $this->get(route('social.callback', 'google'))
        ->assertRedirect(route('user.dashboard'));

    $this->assertAuthenticated();
    $user = User::where('email', 'new@example.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->name)->toBe('New User');
});

it('logs in an existing user matched by email', function () {
    enableGoogle();
    $existing = User::factory()->create(['email' => 'existing@example.com']);
    fakeSocialiteUser('gid-2', 'existing@example.com', 'Existing');

    $this->get(route('social.callback', 'google'))
        ->assertRedirect(route('user.dashboard'));

    expect(auth()->id())->toBe($existing->id);
    expect(User::where('email', 'existing@example.com')->count())->toBe(1);
});

it('blocks an inactive user from social login', function () {
    enableGoogle();
    User::factory()->create(['email' => 'inactive@example.com', 'is_active' => false]);
    fakeSocialiteUser('gid-3', 'inactive@example.com', 'Inactive');

    $this->get(route('social.callback', 'google'))
        ->assertRedirect(route('login'));

    $this->assertGuest();
});
