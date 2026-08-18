<?php

namespace App\Modules\UserWorkspace;

use App\Modules\Shared\Support\BasePanelModule;
use App\Modules\Shared\Support\NavigationBuilder;

class Module extends BasePanelModule
{
    public function id(): string
    {
        return 'user-workspace';
    }

    public function permissions(): array
    {
        return [
            'web' => [
                'user-workspace.view' => 'View own workspace',
                'user-workspace.update' => 'Update own workspace preferences',
            ],
        ];
    }

    public function userNavigation(NavigationBuilder $navigation): void
    {
        $navigation
            ->item(label: 'Workspace', route: 'user.dashboard')
            ->icon('ph-squares-four')
            ->order(5);
    }
}
