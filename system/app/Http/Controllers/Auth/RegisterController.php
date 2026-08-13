<?php

namespace App\Http\Controllers\Auth;

use App\Enums\NotificationTemplateSlug;
use App\Events\UserAutoNotification;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Modules\AuditLog\Services\AuditLogService;
use App\Modules\AuthApi\Services\OtpDeliveryService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLogService,
        protected OtpDeliveryService $otpDeliveryService,
    ) {}

    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        Role::findOrCreate('user', 'web');

        $requireEmailVerification = (bool) setting('require_email_verification', true);
        $requireSmsVerification = (bool) setting('require_sms_verification', false);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        if (! $requireEmailVerification) {
            // With the Email Verification control off, accounts start verified —
            // the Registered listener then skips the verification email.
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        // Assign the hidden default web role
        $user->syncRoles(['user']);

        // Log the registration
        $this->auditLogService->logCustom('register', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        event(new Registered($user));
        event(new UserAutoNotification($user, NotificationTemplateSlug::WELCOME));

        Auth::login($user);

        if ($requireSmsVerification) {
            try {
                $this->otpDeliveryService->send('sms', $user->phone, 'phone verification');
                $status = __('We\'ve sent a verification code to :phone.', ['phone' => $user->phone]);
            } catch (\Throwable $exception) {
                // A misconfigured SMS gateway must not break sign-up — the user
                // can request a new code from the verification screen.
                report($exception);
                $status = __('We could not send the verification code. Please use "Resend code" in a moment.');
            }

            return redirect()->route('user.phone.verification.notice')->with('status', $status);
        }

        return redirect()->route('user.dashboard')
            ->with('success', $requireEmailVerification
                ? __('Registration successful! Please check your email to verify your account.')
                : __('Registration successful! Welcome aboard.'));
    }
}
