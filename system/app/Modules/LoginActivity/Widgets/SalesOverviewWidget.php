<?php

namespace App\Modules\LoginActivity\Widgets;

use App\Modules\Shared\Services\DashboardService;
use App\Modules\Shared\Widgets\ChartWidget;

class SalesOverviewWidget extends ChartWidget
{
    public function __construct(protected DashboardService $dashboardService) {}

    public function id(): string
    {
        return 'admin-sales-overview';
    }

    public function title(): string
    {
        return __('Revenue Overview (Last 6 Months)');
    }

    public function chartType(): string
    {
        return 'area';
    }

    /**
     * Fill the card so it always matches the height of the Payment Health card
     * beside it in the stretched top-row grid. chartHeight() is the minimum.
     */
    public function fillHeight(): bool
    {
        return true;
    }

    public function chartHeight(): int
    {
        return 300;
    }

    protected function getData(): array
    {
        $revenue = $this->dashboardService->getMonthlyRevenue(6);

        return [
            'series' => [
                ['name' => __('Revenue'), 'data' => $revenue['data']],
            ],
            'categories' => $revenue['labels'],
        ];
    }

    protected function getChartOptions(): array
    {
        return [
            'legend' => [
                'show' => false,
            ],
            'markers' => [
                'size' => 3,
                'strokeWidth' => 0,
            ],
            'grid' => [
                'padding' => [
                    'left' => 0,
                    'right' => 0,
                ],
            ],
        ];
    }

    public function position(): int
    {
        return 15;
    }

    public function width(): string
    {
        return 'full';
    }

    public function panel(): string
    {
        return 'admin';
    }
}
