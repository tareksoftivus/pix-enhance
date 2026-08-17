<?php

use App\Modules\PricingPlan\Http\Controllers\Admin\PricingPlanController;
use Illuminate\Support\Facades\Route;

Route::get('pricing-plans/subscribers', [PricingPlanController::class, 'subscribers'])->name('pricing-plan-subscribers.index');
Route::resource('pricing-plans', PricingPlanController::class)->except([]);
Route::post('pricing-plans/bulk-delete', [PricingPlanController::class, 'bulkDelete'])->name('pricing-plans.bulk-delete');
Route::post('pricing-plans/bulk-toggle-status', [PricingPlanController::class, 'bulkToggleStatus'])->name('pricing-plans.bulk-toggle-status');
Route::post('pricing-plans/{pricing_plan}/toggle-status', [PricingPlanController::class, 'toggleStatus'])->name('pricing-plans.toggle-status');
