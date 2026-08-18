<?php

namespace App\Modules\RenderJobs;

use App\Modules\Shared\Support\BasePanelModule;
use App\Modules\Shared\Support\NavigationBuilder;

class Module extends BasePanelModule
{
    public function id(): string
    {
        return 'render-jobs';
    }

    public function permissions(): array
    {
        return [
            'admin' => [
                'render-jobs.view' => 'View render jobs',
                'render-jobs.manage' => 'Manage render jobs',
            ],
            'web' => [
                'render-jobs.view' => 'View own render jobs',
                'render-jobs.create' => 'Create render jobs',
                'render-jobs.delete' => 'Delete own render jobs',
            ],
        ];
    }

    public function adminNavigation(NavigationBuilder $navigation): void
    {
        $navigation
            ->group('AI')
            ->item(label: 'Render Jobs', route: 'admin.render-jobs.index', icon: 'ph-sparkle', permission: 'render-jobs.view')
            ->order(61);
    }
}
