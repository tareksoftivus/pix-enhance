<?php

use App\Modules\Frontend\Database\Seeders\FrontendMenuSeeder;
use App\Modules\Frontend\Database\Seeders\FrontendPageSeeder;
use App\Modules\Frontend\Database\Seeders\FrontendSectionSeeder;
use App\Modules\Frontend\Database\Seeders\FrontendThemeSettingSeeder;
use App\Modules\Frontend\Models\FrontendMenu;
use App\Modules\Frontend\Services\MenuAssignmentService;
use App\Modules\Frontend\Services\MenuService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

test('assigned published menus render in the public theme layout', function () {
    $this->seed([
        FrontendThemeSettingSeeder::class,
        FrontendSectionSeeder::class,
        FrontendPageSeeder::class,
        FrontendMenuSeeder::class,
    ]);

    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee('Resources');
    $response->assertSee('Documentation');
});

test('assigned menus cannot be deleted while still attached to theme slots', function () {
    $this->seed([
        FrontendThemeSettingSeeder::class,
        FrontendSectionSeeder::class,
        FrontendPageSeeder::class,
        FrontendMenuSeeder::class,
    ]);

    $menu = FrontendMenu::query()->where('slug', 'primary-navigation')->firstOrFail();

    expect(fn () => app(MenuService::class)->delete($menu))
        ->toThrow(ValidationException::class);
});

test('footer assignments reject menus that exceed footer depth rules', function () {
    $this->seed([
        FrontendThemeSettingSeeder::class,
        FrontendSectionSeeder::class,
        FrontendPageSeeder::class,
        FrontendMenuSeeder::class,
    ]);

    $menu = FrontendMenu::query()->where('slug', 'primary-navigation')->firstOrFail();

    expect(fn () => app(MenuAssignmentService::class)->validateForSlot('footer', $menu))
        ->toThrow(ValidationException::class);
});
