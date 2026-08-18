<?php

namespace App\Modules\UserWorkspace\Providers;

use App\Modules\Shared\Support\BasePanelModuleProvider;
use App\Modules\UserWorkspace\Services\UserWorkspaceService;

class UserWorkspaceServiceProvider extends BasePanelModuleProvider
{
    public function register(): void
    {
        $this->app->singleton(UserWorkspaceService::class);
    }
}
