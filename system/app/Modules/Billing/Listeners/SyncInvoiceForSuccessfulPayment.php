<?php

namespace App\Modules\Billing\Listeners;

use App\Modules\Billing\Services\BillingService;
use App\Modules\PaymentGateways\Events\PaymentSucceeded;
use Illuminate\Support\Facades\Schema;

class SyncInvoiceForSuccessfulPayment
{
    public function __construct(
        protected BillingService $billing
    ) {}

    public function handle(PaymentSucceeded $event): void
    {
        if (Schema::hasTable('billing_invoices')) {
            $this->billing->invoiceForPayment($event->payment);
        }
    }
}
