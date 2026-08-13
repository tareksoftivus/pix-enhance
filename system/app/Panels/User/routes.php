<?php

use App\Http\Controllers\Auth\PhoneVerificationController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Panels\User\Controllers\DashboardController;
use App\Panels\User\Controllers\ProfileController;
use App\Panels\User\Controllers\SystemNotificationController;
use Illuminate\Support\Facades\Route;

// Two-Factor Challenge (authenticated but not yet 2FA verified)
Route::withoutMiddleware(['2fa', 'verified', 'phone.verified', 'panel:user'])->group(function () {
    Route::get('two-factor-challenge', [TwoFactorController::class, 'challenge'])->name('two-factor.challenge');
    Route::post('two-factor-challenge', [TwoFactorController::class, 'verify'])->name('two-factor.verify');
    Route::post('two-factor-challenge/recovery', [TwoFactorController::class, 'verifyRecoveryCode'])->name('two-factor.verify-recovery');
});

// Phone Verification (authenticated but phone not yet verified). Also skips the
// email gate so users can complete either verification first when both are on.
Route::withoutMiddleware(['phone.verified', 'verified', '2fa', 'panel:user'])->group(function () {
    Route::get('phone/verify', [PhoneVerificationController::class, 'notice'])->name('phone.verification.notice');
    Route::post('phone/verify/send', [PhoneVerificationController::class, 'send'])
        ->middleware('throttle:3,1')
        ->name('phone.verification.send');
    Route::post('phone/verify', [PhoneVerificationController::class, 'verify'])
        ->middleware('throttle:6,1')
        ->name('phone.verification.verify');
});

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Profile
Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('profile/sessions/{session}', [ProfileController::class, 'revokeSession'])->name('profile.sessions.revoke');
Route::delete('profile/sessions', [ProfileController::class, 'revokeAllSessions'])->name('profile.sessions.revoke-all');

// Two-Factor Management (setup/enable/disable — requires full auth)
Route::get('two-factor/setup', [TwoFactorController::class, 'setup'])->name('two-factor.setup');
Route::post('two-factor/enable', [TwoFactorController::class, 'enable'])->name('two-factor.enable');
Route::post('two-factor/disable', [TwoFactorController::class, 'disable'])->name('two-factor.disable');

// System Notifications (bell icon)
Route::get('system-notifications', [SystemNotificationController::class, 'index'])->name('system-notifications.index');
Route::get('system-notifications/unread-count', [SystemNotificationController::class, 'unreadCount'])->name('system-notifications.unread-count');
Route::get('system-notifications/recent', [SystemNotificationController::class, 'recent'])->name('system-notifications.recent');
Route::post('system-notifications/{notification}/mark-read', [SystemNotificationController::class, 'markRead'])->name('system-notifications.mark-read');
Route::post('system-notifications/mark-all-read', [SystemNotificationController::class, 'markAllRead'])->name('system-notifications.mark-all-read');
