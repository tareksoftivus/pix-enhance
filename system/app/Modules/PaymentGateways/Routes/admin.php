<?php

use App\Modules\PaymentGateways\Http\Controllers\Admin\PaymentsController;
use App\Modules\PaymentGateways\Http\Controllers\Admin\RefundsController;
use App\Modules\PaymentGateways\Http\Controllers\Admin\WebhookLogsController;
use Illuminate\Support\Facades\Route;

Route::get('payments', [PaymentsController::class, 'index'])->name('payments.index');
Route::get('payments/{payment}', [PaymentsController::class, 'show'])->name('payments.show');
Route::post('payments/{payment}/refund', [PaymentsController::class, 'refund'])->name('payments.refund');
Route::post('payments/{payment}/approve', [PaymentsController::class, 'approve'])->name('payments.approve');
Route::post('payments/{payment}/reject', [PaymentsController::class, 'reject'])->name('payments.reject');

Route::get('refunds', [RefundsController::class, 'index'])->name('refunds.index');
Route::get('refunds/{refund}', [RefundsController::class, 'show'])->name('refunds.show');

Route::get('webhook-logs', [WebhookLogsController::class, 'index'])->name('webhook-logs.index');
Route::get('webhook-logs/{webhookLog}', [WebhookLogsController::class, 'show'])->name('webhook-logs.show');
