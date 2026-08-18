<?php

use App\Modules\Credits\Http\Controllers\User\CreditCheckoutController;
use Illuminate\Support\Facades\Route;

Route::post('credits/packs', [CreditCheckoutController::class, 'purchasePack'])->name('credits.packs.purchase');
Route::post('credits/plans/{pricingPlan}', [CreditCheckoutController::class, 'purchasePlan'])->name('credits.plans.purchase');
