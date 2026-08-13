<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Http\Request;

/**
 * Setting-aware replacement for the framework 'verified' middleware: the
 * "Email Verification" control in Settings → Controls turns the gate on/off
 * globally without touching route definitions.
 */
class EnsureEmailVerifiedIfRequired extends EnsureEmailIsVerified
{
    /**
     * @param  Request  $request
     */
    public function handle($request, Closure $next, $redirectToRoute = null)
    {
        if (! setting('require_email_verification', true)) {
            return $next($request);
        }

        return parent::handle($request, $next, $redirectToRoute);
    }
}
