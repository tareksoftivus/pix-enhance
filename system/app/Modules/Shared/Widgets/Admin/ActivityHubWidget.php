<?php

namespace App\Modules\Shared\Widgets\Admin;

use App\Modules\LoginActivity\Models\LoginActivity;
use App\Modules\Shared\Services\DashboardService;
use App\Modules\Shared\Widgets\BaseWidget;

class ActivityHubWidget extends BaseWidget
{
    public function __construct(protected DashboardService $dashboardService) {}

    public function id(): string
    {
        return 'admin-activity-hub';
    }

    public function title(): string
    {
        return __('Recent Activity');
    }

    public function render(): string
    {
        $recentPayments = $this->dashboardService->getRecentPayments();
        $recentUsers = $this->dashboardService->getRecentUsers();
        $loginActivities = LoginActivity::with('user')
            ->where('event', 'login')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return $this->view('widgets.admin.activity-hub', compact('recentPayments', 'recentUsers', 'loginActivities'));
    }

    public function position(): int
    {
        return 28;
    }

    public function width(): string
    {
        return 'full';
    }
}
