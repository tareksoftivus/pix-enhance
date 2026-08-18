<?php

namespace App\Modules\Billing\Listeners;

use App\Modules\Billing\Services\BillingService;
use App\Modules\PaymentGateways\Events\PaymentCreated;
use Illuminate\Support\Facades\Schema;

class SyncInvoiceForCreatedPayment
{
    public function __construct(
        protected BillingService $billing
    ) {}

    public function handle(PaymentCreated $event): void
    {
        if (Schema::hasTable('billing_invoices')) {
            $this->billing->invoiceForPayment($event->payment);
        }
    }
}
