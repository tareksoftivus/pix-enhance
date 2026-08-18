<?php

use App\Modules\Billing\Http\Controllers\Admin\BillingController;
use Illuminate\Support\Facades\Route;

Route::get('billing', [BillingController::class, 'index'])->name('billing.index');
Route::get('billing/invoices/{billingInvoice}', [BillingController::class, 'show'])->name('billing.invoices.show');
Route::post('billing/invoices/{billingInvoice}/mark-paid', [BillingController::class, 'markPaid'])->name('billing.invoices.mark-paid');
Route::post('billing/invoices/{billingInvoice}/void', [BillingController::class, 'void'])->name('billing.invoices.void');
