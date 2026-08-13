<?php

namespace App\Modules\Frontend;

use App\Modules\Shared\Support\BasePanelModule;

class Module extends BasePanelModule
{
    public function id(): string
    {
        return 'frontend';
    }

    public function permissions(): array
    {
        return [
            'admin' => [
                'frontend-themes.view' => 'View frontend themes',
                'frontend-themes.edit' => 'Edit frontend themes',
                'frontend-menus.view' => 'View frontend menus',
                'frontend-menus.create' => 'Create frontend menus',
                'frontend-menus.edit' => 'Edit frontend menus',
                'frontend-menus.delete' => 'Delete frontend menus',
                'frontend-menus.publish' => 'Publish frontend menus',
                'frontend-sections.view' => 'View frontend sections',
                'frontend-sections.create' => 'Create frontend sections',
                'frontend-sections.edit' => 'Edit frontend sections',
                'frontend-sections.delete' => 'Delete frontend sections',
                'frontend-pages.view' => 'View frontend pages',
                'frontend-pages.create' => 'Create frontend pages',
                'frontend-pages.edit' => 'Edit frontend pages',
                'frontend-pages.delete' => 'Delete frontend pages',
                'frontend-pages.publish' => 'Publish frontend pages',
            ],
        ];
    }
}
