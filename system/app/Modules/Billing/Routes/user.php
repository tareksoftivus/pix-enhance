<?php

use App\Modules\Billing\Http\Controllers\User\BillingPaymentsController;
use Illuminate\Support\Facades\Route;

Route::get('billing/payments/{payment:uuid}', [BillingPaymentsController::class, 'show'])->name('billing.payments.show');
Route::get('billing/invoices/{billingInvoice}', [BillingPaymentsController::class, 'invoice'])->name('billing.invoices.show');
Route::get('billing/invoices/{billingInvoice}/download', [BillingPaymentsController::class, 'download'])->name('billing.invoices.download');
