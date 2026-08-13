<?php

namespace App\Modules\HomePageSettings\Providers;

use App\Modules\HomePageSettings\Services\HomePageSettingsService;
use App\Modules\Shared\Support\BasePanelModuleProvider;

class HomePageSettingsServiceProvider extends BasePanelModuleProvider
{
    public function register(): void
    {
        $this->app->singleton(HomePageSettingsService::class);
    }
}
