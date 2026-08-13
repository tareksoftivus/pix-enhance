<?php

use App\Modules\Blog\Http\Controllers\Admin\BlogCategoriesController;
use App\Modules\Blog\Http\Controllers\Admin\BlogPostsController;
use Illuminate\Support\Facades\Route;

// Posts
Route::post('blog-posts/bulk-delete', [BlogPostsController::class, 'bulkDelete'])->name('blog-posts.bulk-delete');
Route::resource('blog-posts', BlogPostsController::class)->except(['show']);

// Categories
Route::post('blog-categories/{blog_category}/toggle-status', [BlogCategoriesController::class, 'toggleStatus'])->name('blog-categories.toggle-status');
Route::resource('blog-categories', BlogCategoriesController::class)->except(['show']);
