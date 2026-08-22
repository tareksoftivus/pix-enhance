<?php

namespace App\Modules\SystemNotifications\Providers;

use App\Modules\PaymentGateways\Events\PaymentCreated;
use App\Modules\PaymentGateways\Events\PaymentFailed;
use App\Modules\PaymentGateways\Events\PaymentSucceeded;
use App\Modules\PaymentGateways\Events\RefundProcessed;
use App\Modules\Shared\Support\BasePanelModuleProvider;
use App\Modules\SystemNotifications\Services\SystemNotificationService;
use App\Modules\SystemNotifications\Services\UserSystemNotificationService;
use Illuminate\Support\Facades\Event;

class SystemNotificationsServiceProvider extends BasePanelModuleProvider
{
    public function register(): void
    {
        $this->app->singleton(SystemNotificationService::class);
        $this->app->singleton(UserSystemNotificationService::class);
    }

    protected function bootModule(array $module): void
    {
        Event::listen(PaymentCreated::class, fn (PaymentCreated $event) => app(UserSystemNotificationService::class)->paymentCreated($event->payment->fresh('user') ?? $event->payment));
        Event::listen(PaymentSucceeded::class, fn (PaymentSucceeded $event) => app(UserSystemNotificationService::class)->paymentSucceeded($event->payment->fresh('user') ?? $event->payment));
        Event::listen(PaymentFailed::class, fn (PaymentFailed $event) => app(UserSystemNotificationService::class)->paymentFailed($event->payment->fresh('user') ?? $event->payment));
        Event::listen(RefundProcessed::class, fn (RefundProcessed $event) => app(UserSystemNotificationService::class)->refundProcessed($event->refund->fresh('payment.user') ?? $event->refund));
    }
}
