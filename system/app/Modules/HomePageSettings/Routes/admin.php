<?php

use App\Modules\HomePageSettings\Http\Controllers\Admin\HomePageSettingsController;
use Illuminate\Support\Facades\Route;

Route::get('home-page-settings', [HomePageSettingsController::class, 'index'])->name('home-page-settings.index');
Route::put('home-page-settings', [HomePageSettingsController::class, 'update'])->name('home-page-settings.update');
