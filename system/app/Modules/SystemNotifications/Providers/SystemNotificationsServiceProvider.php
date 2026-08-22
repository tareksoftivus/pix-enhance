<?php

namespace App\Modules\SystemNotifications\Providers;

use App\Models\User;
use App\Modules\PaymentGateways\Events\PaymentCreated;
use App\Modules\PaymentGateways\Events\PaymentFailed;
use App\Modules\PaymentGateways\Events\PaymentSucceeded;
use App\Modules\PaymentGateways\Events\RefundProcessed;
use App\Modules\Shared\Support\BasePanelModuleProvider;
use App\Modules\SystemNotifications\Services\AdminSystemNotificationService;
use App\Modules\SystemNotifications\Services\SystemNotificationService;
use App\Modules\SystemNotifications\Services\UserSystemNotificationService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;

class SystemNotificationsServiceProvider extends BasePanelModuleProvider
{
    public function register(): void
    {
        $this->app->singleton(SystemNotificationService::class);
        $this->app->singleton(UserSystemNotificationService::class);
        $this->app->singleton(AdminSystemNotificationService::class);
    }

    protected function bootModule(array $module): void
    {
        Event::listen(Registered::class, function (Registered $event): void {
            if ($event->user instanceof User) {
                app(AdminSystemNotificationService::class)->userRegistered($event->user);
            }
        });

        Event::listen(PaymentCreated::class, function (PaymentCreated $event): void {
            $payment = $event->payment->fresh('user') ?? $event->payment;

            app(UserSystemNotificationService::class)->paymentCreated($payment);
            app(AdminSystemNotificationService::class)->paymentCreated($payment);
        });

        Event::listen(PaymentSucceeded::class, function (PaymentSucceeded $event): void {
            $payment = $event->payment->fresh('user') ?? $event->payment;

            app(UserSystemNotificationService::class)->paymentSucceeded($payment);
            app(AdminSystemNotificationService::class)->paymentSucceeded($payment);
        });

        Event::listen(PaymentFailed::class, function (PaymentFailed $event): void {
            $payment = $event->payment->fresh('user') ?? $event->payment;

            app(UserSystemNotificationService::class)->paymentFailed($payment);
            app(AdminSystemNotificationService::class)->paymentFailed($payment);
        });

        Event::listen(RefundProcessed::class, function (RefundProcessed $event): void {
            $refund = $event->refund->fresh('payment.user') ?? $event->refund;

            app(UserSystemNotificationService::class)->refundProcessed($refund);
            app(AdminSystemNotificationService::class)->refundProcessed($refund);
        });
    }
}
