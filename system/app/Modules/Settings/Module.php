<?php

namespace App\Modules\Settings;

use App\Modules\Shared\Support\BasePanelModule;
use App\Modules\Shared\Support\NavigationBuilder;

class Module extends BasePanelModule
{
    public function id(): string
    {
        return 'settings';
    }

    public function permissions(): array
    {
        return [
            'admin' => [
                'settings.view' => 'View settings',
                'settings.edit' => 'Edit settings',
            ],
        ];
    }

    public function adminNavigation(NavigationBuilder $navigation): void
    {
        $navigation
            ->group('System')
            ->item(label: 'Settings', route: 'admin.settings.*')
            ->icon('ph-gear')
            ->permission('settings.view')
            ->children([
                ['label' => 'General Settings', 'route' => 'admin.settings.index', 'icon' => 'ph-sliders-horizontal'],
                ['label' => 'Home Page Settings', 'route' => 'admin.home-page-settings.*', 'icon' => 'ph-house'],
                ['label' => 'Frontend Themes', 'route' => 'admin.frontend-themes.*', 'icon' => 'ph-palette'],
                ['label' => 'Menu Management', 'route' => 'admin.frontend-menus.*', 'icon' => 'ph-list'],
                ['label' => 'Manage Frontend', 'route' => 'admin.frontend-sections.*', 'icon' => 'ph-layout'],
                ['label' => 'Manage Pages', 'route' => 'admin.frontend-pages.*', 'icon' => 'ph-files'],
                ['label' => 'Payment Gateways', 'route' => 'admin.payment-gateway-settings.*', 'icon' => 'ph-credit-card'],
                ['label' => 'AI Settings', 'route' => 'admin.ai-settings.*', 'icon' => 'ph-sparkle'],
                ['label' => 'Currencies', 'route' => 'admin.currencies.*', 'icon' => 'ph-currency-circle-dollar'],
            ])
            ->order(130);
    }
}
