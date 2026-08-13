<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Modules\AuthApi\Services\OtpDeliveryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PhoneVerificationController extends Controller
{
    public function __construct(
        protected OtpDeliveryService $otpDeliveryService
    ) {}

    /**
     * Show the phone verification screen. Users may also set or correct their
     * number here — accounts created before SMS verification was enabled have
     * no phone on record.
     */
    public function notice(Request $request): View|RedirectResponse
    {
        if ($request->user()->phone_verified_at !== null) {
            return redirect()->route('user.dashboard');
        }

        return view('auth.verify-phone');
    }

    /**
     * Send (or resend) the verification code, updating the phone number first
     * when one is submitted.
     */
    public function send(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'phone' => ($user->phone ? 'nullable' : 'required').'|string|max:20|unique:users,phone,'.$user->id,
        ]);

        if (! empty($validated['phone']) && $validated['phone'] !== $user->phone) {
            $user->forceFill(['phone' => $validated['phone'], 'phone_verified_at' => null])->save();
        }

        try {
            $this->otpDeliveryService->send('sms', $user->phone, 'phone verification');
        } catch (\Throwable $exception) {
            report($exception);

            return back()->with('error', __('The verification SMS could not be sent. Please try again shortly or contact support.'));
        }

        return back()->with('status', __('A verification code has been sent to :phone.', ['phone' => $user->phone]));
    }

    /**
     * Confirm the code and mark the phone as verified.
     */
    public function verify(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user->phone) {
            return redirect()->route('user.phone.verification.notice');
        }

        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $this->otpDeliveryService->verify('sms', $user->phone, $request->input('otp'));

        $user->forceFill(['phone_verified_at' => now()])->save();

        return redirect()->route('user.dashboard')
            ->with('success', __('Phone number verified.'));
    }
}
