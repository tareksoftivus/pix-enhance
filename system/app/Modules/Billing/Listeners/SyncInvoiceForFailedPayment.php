<?php

namespace App\Modules\Billing\Listeners;

use App\Modules\Billing\Services\BillingService;
use App\Modules\PaymentGateways\Events\PaymentFailed;
use Illuminate\Support\Facades\Schema;

class SyncInvoiceForFailedPayment
{
    public function __construct(
        protected BillingService $billing
    ) {}

    public function handle(PaymentFailed $event): void
    {
        if (Schema::hasTable('billing_invoices')) {
            $this->billing->invoiceForPayment($event->payment);
        }
    }
}
