<?php

namespace App\Modules\PaymentGateways;

use App\Modules\PaymentGateways\Models\Payment;
use App\Modules\PaymentGateways\Models\Refund;
use App\Modules\PaymentGateways\Models\WebhookLog;
use App\Modules\PaymentGateways\Policies\PaymentPolicy;
use App\Modules\PaymentGateways\Policies\RefundPolicy;
use App\Modules\PaymentGateways\Policies\WebhookLogPolicy;
use App\Modules\Shared\Support\BasePanelModule;
use App\Modules\Shared\Support\NavigationBuilder;

class Module extends BasePanelModule
{
    public function id(): string
    {
        return 'payment-gateways';
    }

    public function permissions(): array
    {
        return [
            'admin' => [
                'payments.view' => 'View payments',
                'payments.refund' => 'Refund payments',
                'payments.approve' => 'Approve payments',
                'refunds.view' => 'View refunds',
                'webhook-logs.view' => 'View webhook logs',
            ],
        ];
    }

    public function policies(): array
    {
        return [
            Payment::class => PaymentPolicy::class,
            Refund::class => RefundPolicy::class,
            WebhookLog::class => WebhookLogPolicy::class,
        ];
    }

    public function adminNavigation(NavigationBuilder $navigation): void
    {
        $navigation
            ->group('Management')
            ->item(label: 'Payments', route: 'admin.payments.*')
            ->icon('ph-credit-card')
            ->permission('payments.view')
            ->children([
                ['label' => 'All Payments', 'route' => 'admin.payments.*', 'icon' => 'ph-credit-card'],
                ['label' => 'Refunds', 'route' => 'admin.refunds.*', 'icon' => 'ph-arrow-counter-clockwise'],
                ['label' => 'Webhook Logs', 'route' => 'admin.webhook-logs.*', 'icon' => 'ph-webhooks-logo'],
            ])
            ->order(240);
    }
}
