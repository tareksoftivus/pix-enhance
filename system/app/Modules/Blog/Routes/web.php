<?php

use App\Modules\Blog\Http\Controllers\BlogController;
use Illuminate\Support\Facades\Route;

Route::get('blog', [BlogController::class, 'index'])->name('blog.index');
// Sitemap must precede the {slug} route so "sitemap.xml" isn't treated as a post.
Route::get('blog/sitemap.xml', [BlogController::class, 'sitemap'])->name('blog.sitemap');
Route::get('blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
