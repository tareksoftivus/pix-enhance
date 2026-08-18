<?php

namespace App\Modules\Credits;

use App\Modules\Shared\Support\BasePanelModule;
use App\Modules\Shared\Support\NavigationBuilder;

class Module extends BasePanelModule
{
    public function id(): string
    {
        return 'credits';
    }

    public function permissions(): array
    {
        return [
            'admin' => [
                'credits.view' => 'View credit wallets and ledger',
                'credits.adjust' => 'Adjust user credit balances',
            ],
            'web' => [
                'credits.view' => 'View own credit balance',
                'credits.purchase' => 'Purchase credits and plans',
            ],
        ];
    }

    public function adminNavigation(NavigationBuilder $navigation): void
    {
        $navigation
            ->group('Billing')
            ->item(label: 'Credits', route: 'admin.credits.index', icon: 'ph-coins', permission: 'credits.view')
            ->order(92);
    }
}
