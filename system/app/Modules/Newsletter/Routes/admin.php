<?php

use App\Modules\Newsletter\Http\Controllers\Admin\SubscribersController;
use Illuminate\Support\Facades\Route;

Route::post('subscribers/bulk-delete', [SubscribersController::class, 'bulkDelete'])->name('subscribers.bulk-delete');
Route::resource('subscribers', SubscribersController::class)
    ->parameters(['subscribers' => 'subscriber'])
    ->only(['index', 'destroy']);
Route::post('subscribers/{subscriber}/toggle-status', [SubscribersController::class, 'toggleStatus'])->name('subscribers.toggle-status');
