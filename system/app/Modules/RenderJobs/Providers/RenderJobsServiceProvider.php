<?php

namespace App\Modules\RenderJobs\Providers;

use App\Modules\RenderJobs\Services\LocalRenderProcessor;
use App\Modules\RenderJobs\Services\RenderJobService;
use App\Modules\Shared\Support\BasePanelModuleProvider;

class RenderJobsServiceProvider extends BasePanelModuleProvider
{
    public function register(): void
    {
        $this->app->singleton(LocalRenderProcessor::class);
        $this->app->singleton(RenderJobService::class);
    }
}
