<?php

use App\Modules\Frontend\Database\Seeders\FrontendPageSeeder;
use App\Modules\Frontend\Database\Seeders\FrontendSectionSeeder;
use App\Modules\Frontend\Database\Seeders\FrontendThemeSettingSeeder;
use App\Modules\Frontend\Models\FrontendSection;
use App\Modules\Frontend\Models\Page;
use App\Modules\Frontend\Services\ThemeRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('theme registry resolves installed themes', function () {
    $registry = app(ThemeRegistry::class);

    expect(array_keys($registry->all()))
        ->toContain('classic')
        ->toContain('studio');
});

test('published home page renders with seeded frontend content', function () {
    $this->seed([
        FrontendThemeSettingSeeder::class,
        FrontendSectionSeeder::class,
        FrontendPageSeeder::class,
    ]);

    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee('Launch pages that feel intentional.');
});

test('unsupported section types use the fallback renderer publicly', function () {
    $this->seed([
        FrontendThemeSettingSeeder::class,
    ]);

    $page = Page::create([
        'title' => 'Fallback Demo',
        'slug' => 'fallback-demo',
        'status' => 'published',
        'default_layout' => 'default',
        'is_home' => false,
        'is_system' => false,
        'published_at' => now(),
    ]);

    $section = FrontendSection::create([
        'name' => 'Legacy Banner',
        'slug' => 'legacy-banner',
        'type' => 'legacy_banner',
        'status' => 'published',
        'data' => [],
        'theme_overrides' => [],
    ]);

    $page->pageSections()->create([
        'frontend_section_id' => $section->id,
        'sort_order' => 0,
    ]);

    $response = $this->get('/fallback-demo');

    $response->assertOk();
    $response->assertSee('This section is not supported by the current theme.');
});
