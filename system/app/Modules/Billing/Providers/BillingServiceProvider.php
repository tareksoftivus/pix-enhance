<?php

namespace App\Modules\Billing\Providers;

use App\Modules\Billing\Listeners\SyncInvoiceForCreatedPayment;
use App\Modules\Billing\Listeners\SyncInvoiceForFailedPayment;
use App\Modules\Billing\Listeners\SyncInvoiceForRefund;
use App\Modules\Billing\Listeners\SyncInvoiceForSuccessfulPayment;
use App\Modules\Billing\Services\BillingService;
use App\Modules\PaymentGateways\Events\PaymentCreated;
use App\Modules\PaymentGateways\Events\PaymentFailed;
use App\Modules\PaymentGateways\Events\PaymentSucceeded;
use App\Modules\PaymentGateways\Events\RefundProcessed;
use App\Modules\Shared\Support\BasePanelModuleProvider;
use Illuminate\Support\Facades\Event;

class BillingServiceProvider extends BasePanelModuleProvider
{
    public function register(): void
    {
        $this->app->singleton(BillingService::class);
    }

    protected function bootModule(array $module): void
    {
        Event::listen(PaymentCreated::class, SyncInvoiceForCreatedPayment::class);
        Event::listen(PaymentSucceeded::class, SyncInvoiceForSuccessfulPayment::class);
        Event::listen(PaymentFailed::class, SyncInvoiceForFailedPayment::class);
        Event::listen(RefundProcessed::class, SyncInvoiceForRefund::class);
    }
}
