<?php

use App\Http\Controllers\WebhookController;
use App\Modules\PaymentGateways\Http\Controllers\PaymentActionController;
use App\Modules\PaymentGateways\Http\Controllers\PaymentReturnController;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('payments/action/{payment:uuid}', [PaymentActionController::class, 'show'])
        ->name('payments.action');
    Route::post('payments/action/{payment:uuid}', [PaymentActionController::class, 'complete'])
        ->withoutMiddleware([ValidateCsrfToken::class])
        ->name('payments.action.complete');
    Route::get('payments/{gateway}/return', [PaymentReturnController::class, 'return'])
        ->name('payments.return');
    Route::get('payments/{gateway}/cancel', [PaymentReturnController::class, 'cancel'])
        ->name('payments.cancel');
});

Route::post('webhooks/payments/{gateway}', [WebhookController::class, 'handle'])
    ->withoutMiddleware([ValidateCsrfToken::class])
    ->name('webhooks.payments');
