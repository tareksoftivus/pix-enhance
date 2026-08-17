<?php

namespace App\Modules\PricingPlan;

use App\Modules\Shared\Support\BasePanelModule;
use App\Modules\Shared\Support\NavigationBuilder;

class Module extends BasePanelModule
{
    public function id(): string
    {
        return 'pricing-plans';
    }

    public function permissions(): array
    {
        return [
            'admin' => [
                'pricing-plans.view' => 'View pricing plans',
                'pricing-plans.create' => 'Create pricing plans',
                'pricing-plans.edit' => 'Edit pricing plans',
                'pricing-plans.delete' => 'Delete pricing plans',
            ],
        ];
    }

    public function adminNavigation(NavigationBuilder $navigation): void
    {
        $navigation
            ->group('Billing')
            ->item(label: 'Pricing Plans', route: 'admin.pricing-plans.*', icon: 'ph-currency-dollar', permission: 'pricing-plans.view')
            ->order(90)
            ->item(label: 'Subscribers', route: 'admin.pricing-plan-subscribers.index', icon: 'ph-users-three', permission: 'pricing-plans.view')
            ->order(91);
    }
}
