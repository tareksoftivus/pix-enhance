<?php

namespace App\Modules\HomePageSettings\Tests\Feature;

use App\Modules\Shared\Support\ModuleRegistry;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class HomePageSettingsModuleTest extends TestCase
{
    public function test_home_page_settings_module_is_registered(): void
    {
        $module = app(ModuleRegistry::class)->find('home-page-settings');

        $this->assertNotNull($module);
        $this->assertNotNull($module['descriptor']);
        $this->assertTrue(Route::has('admin.home-page-settings.index'));
    }
}
