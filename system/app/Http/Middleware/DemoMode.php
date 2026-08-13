<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Blocks data-changing requests inside the admin panel while the app runs as a
 * public demo, so visitors cannot alter the showcased content.
 *
 * Only the admin panel is affected: the public site, the employee dashboard and
 * the organization panel stay fully interactive so the product can be tried out.
 *
 * Enabled with DEMO_MODE=true in .env. Deliberately env-driven (not a database
 * setting) so a demo visitor cannot switch it off from inside the demo itself.
 */
class DemoMode
{
    /**
     * Admin route name patterns that stay writable so visitors can still sign
     * in and out, reset a password and complete two-factor challenges.
     *
     * @var list<string>
     */
    protected array $allowedRoutePatterns = [
        'admin.login',
        'admin.login.*',
        'admin.logout',
        'admin.password.*',
        'admin.two-factor.*',
        'admin.2fa.*',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (! config('app.demo_mode')) {
            return $next($request);
        }

        if ($request->isMethodSafe()) {
            return $next($request);
        }

        if (! $this->isAdminPanelRequest($request)) {
            return $next($request);
        }

        if ($request->routeIs(...$this->allowedRoutePatterns)) {
            return $next($request);
        }

        $message = __('This is a demo. Admin changes are disabled so everyone sees the same content.');

        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 403);
        }

        return back()->with('error', $message);
    }

    /**
     * Whether the request targets the admin panel, based on its configured prefix.
     */
    protected function isAdminPanelRequest(Request $request): bool
    {
        $prefix = config('panels.admin.prefix', 'admin');

        return $request->is($prefix, $prefix.'/*');
    }
}
