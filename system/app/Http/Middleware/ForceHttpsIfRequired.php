<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Redirect plain-HTTP traffic to HTTPS when the "Force HTTPS" control in
 * Settings → Controls is enabled, and pin URL generation to the https scheme.
 */
class ForceHttpsIfRequired
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->forceHttps()) {
            return $next($request);
        }

        URL::forceScheme('https');

        if (! $request->secure()) {
            return redirect()->secure($request->getRequestUri(), 301);
        }

        return $next($request);
    }

    protected function forceHttps(): bool
    {
        try {
            return (bool) setting('force_https', false);
        } catch (Throwable) {
            // Settings table not ready (fresh install) — never block traffic.
            return false;
        }
    }
}
