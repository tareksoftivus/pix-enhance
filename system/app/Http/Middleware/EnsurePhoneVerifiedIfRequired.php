<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gate user-panel access behind phone verification when the "SMS Verification"
 * control in Settings → Controls is enabled. Users without a verified phone are
 * sent to the phone verification screen, where they can also set their number
 * (covers accounts created before the control was switched on).
 */
class EnsurePhoneVerifiedIfRequired
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! setting('require_sms_verification', false)) {
            return $next($request);
        }

        $user = $request->user();

        if (! $user || $user->phone_verified_at !== null) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            abort(403, __('Your phone number is not verified.'));
        }

        return redirect()->route('user.phone.verification.notice');
    }
}
