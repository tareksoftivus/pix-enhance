<?php

namespace App\Modules\AiSettings;

use App\Modules\Shared\Support\BasePanelModule;

class Module extends BasePanelModule
{
    public function id(): string
    {
        return 'ai-settings';
    }

    public function permissions(): array
    {
        return [
            'admin' => [
                'ai-settings.view' => 'View AI settings',
                'ai-settings.edit' => 'Edit AI settings',
            ],
        ];
    }
}
