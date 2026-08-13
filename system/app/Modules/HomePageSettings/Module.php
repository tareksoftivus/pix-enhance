<?php

namespace App\Modules\HomePageSettings;

use App\Modules\Shared\Support\BasePanelModule;

class Module extends BasePanelModule
{
    public function id(): string
    {
        return 'home-page-settings';
    }

    public function permissions(): array
    {
        return [
            'admin' => [
                'home-page-settings.view' => 'View home page settings',
                'home-page-settings.edit' => 'Edit home page settings',
            ],
        ];
    }
}
