<?php

namespace App\Modules\Currencies\Tests\Feature;

use App\Modules\Shared\Support\ModuleRegistry;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class CurrenciesModuleTest extends TestCase
{
    public function test_currencies_module_is_registered(): void
    {
        $module = app(ModuleRegistry::class)->find('currencies');

        $this->assertNotNull($module);
        $this->assertNotNull($module['descriptor']);
        $this->assertTrue(Route::has('admin.currencies.index'));
    }
}
