<?php

use App\Modules\RenderJobs\Http\Controllers\User\RenderJobsController;
use Illuminate\Support\Facades\Route;

Route::post('render-jobs', [RenderJobsController::class, 'store'])->name('render-jobs.store');
Route::delete('render-jobs', [RenderJobsController::class, 'clearHistory'])->name('render-jobs.clear-history');
Route::get('render-jobs/{renderJob}', [RenderJobsController::class, 'show'])->name('render-jobs.show');
Route::get('render-jobs/{renderJob}/download', [RenderJobsController::class, 'download'])->name('render-jobs.download');
Route::post('render-jobs/{renderJob}/cancel', [RenderJobsController::class, 'cancel'])->name('render-jobs.cancel');
Route::delete('render-jobs/{renderJob}', [RenderJobsController::class, 'destroy'])->name('render-jobs.destroy');
