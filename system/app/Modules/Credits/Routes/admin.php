<?php

use App\Modules\Credits\Http\Controllers\Admin\CreditTransactionsController;
use Illuminate\Support\Facades\Route;

Route::get('credits', [CreditTransactionsController::class, 'index'])->name('credits.index');
Route::post('credits/adjustments', [CreditTransactionsController::class, 'storeAdjustment'])->name('credits.adjustments.store');
