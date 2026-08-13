<?php

namespace App\Modules\Support\Providers;

use App\Modules\Shared\Support\BasePanelModuleProvider;
use App\Modules\Support\Widgets\SupportSnapshotWidget;
use App\Services\WidgetRegistry;

class SupportServiceProvider extends BasePanelModuleProvider
{
    public function register(): void
    {
        //
    }

    protected function bootModule(array $module): void
    {
        if ($this->app->bound(WidgetRegistry::class)) {
            $this->app->make(WidgetRegistry::class)->register(new SupportSnapshotWidget);
        }
    }
}
