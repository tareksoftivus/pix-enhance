<?php

use App\Models\Admin;
use App\Modules\Newsletter\Models\Subscriber;
use App\Modules\Shared\Support\ModuleRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('loads the module descriptor', function (): void {
    expect(app(ModuleRegistry::class)->find('newsletter'))->not->toBeNull();
});

it('subscribes and reactivates an email from the public endpoint', function (): void {
    $subscriber = Subscriber::factory()->inactive()->create([
        'email' => 'team@northwind.example',
    ]);

    $this
        ->post(route('newsletter.subscribe'), ['email' => 'TEAM@NORTHWIND.EXAMPLE'])
        ->assertRedirect()
        ->assertSessionHas('newsletter_success');

    expect(Subscriber::query()->where('email', 'team@northwind.example')->count())->toBe(1);
    expect($subscriber->fresh()->active)->toBeTrue();
});

it('can access the subscriber admin index', function (): void {
    Permission::findOrCreate('newsletter.view', 'admin');

    $admin = Admin::factory()->create([
        'is_active' => true,
        'email_verified_at' => now(),
    ]);
    $admin->givePermissionTo(['newsletter.view']);

    Subscriber::factory()->create([
        'email' => 'content@northwind.example',
    ]);

    $this
        ->actingAs($admin, 'admin')
        ->get(route('admin.subscribers.index'))
        ->assertOk()
        ->assertViewHas('subscribers')
        ->assertSee('content@northwind.example');
});
