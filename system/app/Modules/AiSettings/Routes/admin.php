<?php

use App\Modules\AiSettings\Http\Controllers\Admin\AiSettingsController;
use Illuminate\Support\Facades\Route;

Route::get('ai-settings', [AiSettingsController::class, 'index'])->name('ai-settings.index');
Route::put('ai-settings', [AiSettingsController::class, 'update'])->name('ai-settings.update');
