<?php

namespace App\Modules\Languages;

use App\Modules\Languages\Models\Language;
use App\Modules\Languages\Policies\LanguagePolicy;
use App\Modules\Shared\Support\BasePanelModule;
use App\Modules\Shared\Support\NavigationBuilder;

class Module extends BasePanelModule
{
    public function id(): string
    {
        return 'languages';
    }

    public function permissions(): array
    {
        return [
            'admin' => [
                'languages.view' => 'View languages',
                'languages.create' => 'Create languages',
                'languages.edit' => 'Edit languages',
                'languages.delete' => 'Delete languages',
            ],
        ];
    }

    public function policies(): array
    {
        return [
            Language::class => LanguagePolicy::class,
        ];
    }

    public function adminNavigation(NavigationBuilder $navigation): void
    {
        $navigation
            ->group('System')
            ->item(label: 'Languages', route: 'admin.languages.*')
            ->icon('ph-translate')
            ->permission('languages.view')
            ->order(120);
    }
}
