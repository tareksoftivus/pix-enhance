<?php

namespace App\Modules\Billing\Listeners;

use App\Modules\Billing\Services\BillingService;
use App\Modules\PaymentGateways\Events\RefundProcessed;
use Illuminate\Support\Facades\Schema;

class SyncInvoiceForRefund
{
    public function __construct(
        protected BillingService $billing
    ) {}

    public function handle(RefundProcessed $event): void
    {
        if (Schema::hasTable('billing_invoices')) {
            $this->billing->syncRefund($event->refund);
        }
    }
}
