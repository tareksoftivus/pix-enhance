<?php

use App\Models\Admin;
use App\Modules\Shared\Support\ModuleRegistry;
use App\Modules\Testimonials\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('loads the module descriptor', function (): void {
    expect(app(ModuleRegistry::class)->find('testimonials'))->not->toBeNull();
});

it('can access edit testimonial page', function (): void {
    Permission::findOrCreate('testimonials.view', 'admin');
    Permission::findOrCreate('testimonials.edit', 'admin');

    $admin = Admin::factory()->create([
        'is_active' => true,
        'email_verified_at' => now(),
    ]);
    $admin->givePermissionTo(['testimonials.view', 'testimonials.edit']);

    $testimonial = Testimonial::query()->create([
        'client_name' => 'Marta Kovac',
        'company_name' => 'Northwind',
        'designation' => 'Head of Content',
        'quote' => 'PixEnhance rescued our product catalogue.',
        'rating' => 5,
        'sort_order' => 1,
        'active' => true,
    ]);

    $this
        ->actingAs($admin, 'admin')
        ->get(route('admin.testimonials.edit', $testimonial))
        ->assertOk()
        ->assertViewHas('testimonial')
        ->assertSee('Marta Kovac');
});

it('renders active testimonials in the enhance frontend section', function (): void {
    Testimonial::query()->create([
        'client_name' => 'Priya Raghunathan',
        'company_name' => 'Orbitly',
        'designation' => 'Staff Engineer',
        'quote' => 'The PixEnhance API keeps our listing pipeline fast.',
        'rating' => 5,
        'sort_order' => 1,
        'active' => true,
    ]);

    $this
        ->view('frontend.themes.enhance.sections.testimonials')
        ->assertSee('Priya Raghunathan')
        ->assertSee('The PixEnhance API keeps our listing pipeline fast.');
});
