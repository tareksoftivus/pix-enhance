<?php

use App\Modules\Currencies\Http\Controllers\Admin\CurrenciesController;
use Illuminate\Support\Facades\Route;

Route::resource('currencies', CurrenciesController::class);
Route::post('currencies/{currency}/toggle-status', [CurrenciesController::class, 'toggleStatus'])->name('currencies.toggle-status');
