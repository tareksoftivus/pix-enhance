<?php

use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\FrontendPageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [FrontendPageController::class, 'home'])->name('home');

// Guest Routes (Auth)
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->middleware('throttle:login');

    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);

    Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

    Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

    // Social Login (OAuth via Socialite). The callback URL is derived from the
    // 'social.callback' route in SettingsServiceProvider — keep this name in sync.
    Route::get('auth/{provider}/redirect', [SocialLoginController::class, 'redirect'])
        ->whereIn('provider', ['google', 'facebook', 'github'])
        ->name('social.redirect');
    Route::get('auth/{provider}/callback', [SocialLoginController::class, 'callback'])
        ->whereIn('provider', ['google', 'facebook', 'github'])
        ->name('social.callback');
});

// Language Switcher
Route::post('/locale', function (Request $request) {
    $request->validate(['locale' => 'required|string|max:10']);

    $locale = $request->input('locale');

    // Verify a lang/{locale}.json file exists
    if (file_exists(lang_path("{$locale}.json"))) {
        session(['locale' => $locale]);
    }

    return redirect()->back();
})->name('locale.switch')->middleware('web');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('logout', [LogoutController::class, 'logout'])->name('logout');

    // Email Verification
    Route::get('email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->middleware('signed')->name('verification.verify');
    Route::post('email/resend', [EmailVerificationController::class, 'resend'])->middleware('throttle:6,1')->name('verification.send');
});

Route::get('{slug}', [FrontendPageController::class, 'show'])
    ->where('slug', '^(?!admin$|dashboard$|login$|register$|forgot-password$|reset-password$|locale$|storage$|blog$).+')
    ->name('frontend.page');
