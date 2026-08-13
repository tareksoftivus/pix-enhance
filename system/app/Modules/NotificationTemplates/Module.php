<?php

namespace App\Modules\NotificationTemplates;

use App\Modules\NotificationTemplates\Models\NotificationLog;
use App\Modules\NotificationTemplates\Models\NotificationTemplate;
use App\Modules\NotificationTemplates\Policies\NotificationLogPolicy;
use App\Modules\NotificationTemplates\Policies\NotificationTemplatePolicy;
use App\Modules\Shared\Support\BasePanelModule;
use App\Modules\Shared\Support\NavigationBuilder;

class Module extends BasePanelModule
{
    public function id(): string
    {
        return 'notification-templates';
    }

    public function permissions(): array
    {
        return [
            'admin' => [
                'notification-templates.view' => 'View notification templates',
                'notification-templates.edit' => 'Edit notification templates',
                'notification-logs.view' => 'View notification logs',
            ],
        ];
    }

    public function policies(): array
    {
        return [
            NotificationTemplate::class => NotificationTemplatePolicy::class,
            NotificationLog::class => NotificationLogPolicy::class,
        ];
    }

    public function adminNavigation(NavigationBuilder $navigation): void
    {
        $navigation
            ->group('Management')
            ->item(label: 'Notifications', route: 'admin.notification-templates.*')
            ->icon('ph-bell')
            ->permission('notification-templates.view')
            ->children([
                ['label' => 'Templates', 'route' => 'admin.notification-templates.*', 'icon' => 'ph-file-text'],
                ['label' => 'Send Notification', 'route' => 'admin.notification-send.create', 'permission' => 'system-notifications.send', 'icon' => 'ph-paper-plane-tilt'],
                ['label' => 'Logs', 'route' => 'admin.notification-logs.*', 'icon' => 'ph-clock-counter-clockwise'],
                ['label' => 'System Notifications', 'route' => 'admin.system-notifications.*', 'icon' => 'ph-bell-ringing'],
            ])
            ->order(35);
    }
}
