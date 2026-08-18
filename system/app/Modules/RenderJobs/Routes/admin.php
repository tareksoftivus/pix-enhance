<?php

use App\Modules\RenderJobs\Http\Controllers\Admin\RenderJobsController;
use Illuminate\Support\Facades\Route;

Route::get('render-jobs', [RenderJobsController::class, 'index'])->name('render-jobs.index');
Route::post('render-jobs/{renderJob}/retry', [RenderJobsController::class, 'retry'])->name('render-jobs.retry');
Route::post('render-jobs/{renderJob}/cancel', [RenderJobsController::class, 'cancel'])->name('render-jobs.cancel');
