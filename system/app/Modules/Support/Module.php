<?php

namespace App\Modules\Support;

use App\Modules\Shared\Support\BasePanelModule;
use App\Modules\Shared\Support\NavigationBuilder;
use App\Modules\Support\Models\SupportTicket;
use App\Modules\Support\Policies\SupportTicketPolicy;

class Module extends BasePanelModule
{
    public function id(): string
    {
        return 'support';
    }

    public function permissions(): array
    {
        return [
            // Super-admins manage every ticket (super-admin bypasses via Gate::before).
            'admin' => [
                'support-tickets.view' => 'View support tickets',
                'support-tickets.reply' => 'Reply to support tickets',
                'support-tickets.edit' => 'Update support ticket status',
                'support-tickets.delete' => 'Delete support tickets',
            ],
            // Web-guard permissions for the user panel. The 'user' role holds '*',
            // so these are granted automatically; ownership is enforced in the service.
            'web' => [
                'support-tickets.view' => 'View own support tickets',
                'support-tickets.create' => 'Open support tickets',
                'support-tickets.reply' => 'Reply to own support tickets',
            ],
        ];
    }

    public function policies(): array
    {
        return [
            SupportTicket::class => SupportTicketPolicy::class,
        ];
    }

    public function adminNavigation(NavigationBuilder $navigation): void
    {
        $navigation
            ->group('Management')
            ->item(label: 'Support Tickets', route: 'admin.support-tickets.*')
            ->icon('ph-lifebuoy')
            ->permission('support-tickets.view')
            ->order(40);
    }

    public function userNavigation(NavigationBuilder $navigation): void
    {
        $navigation
            ->item(label: 'Support', route: 'user.support-tickets.*')
            ->icon('ph-lifebuoy')
            ->order(30);
    }
}
