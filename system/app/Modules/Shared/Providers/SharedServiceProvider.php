<?php

namespace App\Modules\Shared\Providers;

use App\Modules\Shared\Services\DashboardService;
use App\Modules\Shared\Support\BasePanelModuleProvider;
use App\Modules\Shared\Widgets\Admin\ActivityHubWidget;
use App\Modules\Shared\Widgets\Admin\AdminHealthWidget;
use App\Modules\Shared\Widgets\Admin\RecentActivityWidget;
use App\Modules\Shared\Widgets\Admin\StatsWidget;
use App\Modules\Shared\Widgets\Admin\UserDistributionChartWidget;
use App\Modules\Shared\Widgets\User\QuickLinksWidget;
use App\Modules\Shared\Widgets\User\WelcomeWidget;
use App\Services\WidgetRegistry;

class SharedServiceProvider extends BasePanelModuleProvider
{
    public function register(): void
    {
        //
    }

    protected function bootModule(array $module): void
    {
        $registry = $this->app->make(WidgetRegistry::class);
        $dashboardService = $this->app->make(DashboardService::class);

        $registry->register(new StatsWidget($dashboardService));
        $registry->register(new AdminHealthWidget($dashboardService));
        $registry->register(new RecentActivityWidget($dashboardService));
        $registry->register(new ActivityHubWidget($dashboardService));
        $registry->register(new UserDistributionChartWidget($dashboardService));

        $registry->register(new WelcomeWidget);
        $registry->register(new QuickLinksWidget);
    }
}
