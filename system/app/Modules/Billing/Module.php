<?php

namespace App\Modules\Billing;

use App\Modules\Shared\Support\BasePanelModule;
use App\Modules\Shared\Support\NavigationBuilder;

class Module extends BasePanelModule
{
    public function id(): string
    {
        return 'billing';
    }

    public function permissions(): array
    {
        return [
            'admin' => [
                'billing.view' => 'View billing overview and invoices',
                'billing.manage' => 'Manage invoices',
            ],
            'web' => [
                'billing.view' => 'View own billing records',
                'billing.download' => 'Download own invoices and receipts',
            ],
        ];
    }

    public function adminNavigation(NavigationBuilder $navigation): void
    {
        $navigation
            ->group('Billing')
            ->item(label: 'Billing', route: 'admin.billing.*', icon: 'ph-receipt', permission: 'billing.view')
            ->order(90);
    }
}
