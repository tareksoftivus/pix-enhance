<?php

use App\Modules\Testimonials\Http\Controllers\Admin\TestimonialsController;
use Illuminate\Support\Facades\Route;

Route::post('testimonials/bulk-delete', [TestimonialsController::class, 'bulkDelete'])->name('testimonials.bulk-delete');
Route::resource('testimonials', TestimonialsController::class)
    ->parameters(['testimonials' => 'testimonial'])
    ->except(['show']);
Route::post('testimonials/{testimonial}/toggle-status', [TestimonialsController::class, 'toggleStatus'])->name('testimonials.toggle-status');
