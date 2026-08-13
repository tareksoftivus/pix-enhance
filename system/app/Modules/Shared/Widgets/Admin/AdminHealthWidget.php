<?php

namespace App\Modules\Shared\Widgets\Admin;

use App\Modules\Shared\Services\DashboardService;
use App\Modules\Shared\Widgets\BaseWidget;

class AdminHealthWidget extends BaseWidget
{
    public function __construct(protected DashboardService $dashboardService) {}

    public function id(): string
    {
        return 'admin-health';
    }

    public function title(): string
    {
        return __('Payment Health');
    }

    public function render(): string
    {
        $metrics = $this->dashboardService->getPaymentHealthMetrics();

        return $this->view('widgets.admin.admin-health', compact('metrics'));
    }

    public function position(): int
    {
        return 17;
    }

    public function cacheFor(): ?int
    {
        return 300;
    }
}
