<?php

use App\Http\Middleware\DemoMode;
use App\Http\Middleware\EnsureEmailVerifiedIfRequired;
use App\Http\Middleware\EnsurePhoneVerifiedIfRequired;
use App\Http\Middleware\EnsureTwoFactorAuthenticated;
use App\Http\Middleware\ForceHttpsIfRequired;
use App\Http\Middleware\PanelAccess;
use App\Http\Middleware\SetDynamicRootUrl;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\ShareImpersonation;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(prepend: [
            ForceHttpsIfRequired::class,
            SetDynamicRootUrl::class,
        ]);

        $middleware->web(append: [
            SetLocale::class,
            ShareImpersonation::class,
            DemoMode::class,
        ]);

        $middleware->api(append: [
            DemoMode::class,
        ]);

        $middleware->alias([
            'panel' => PanelAccess::class,
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            '2fa' => EnsureTwoFactorAuthenticated::class,
            // Setting-aware gates: the Email/SMS Verification controls in
            // Settings → Controls switch these on/off globally.
            'verified' => EnsureEmailVerifiedIfRequired::class,
            'phone.verified' => EnsurePhoneVerifiedIfRequired::class,
        ]);

        // Send unauthenticated guests to the login screen of the panel they were
        // trying to reach — an expired admin session must return to admin/login,
        // not the public user login. Panel prefixes/login routes come from config.
        $middleware->redirectGuestsTo(function (Request $request): string {
            foreach (config('panels', []) as $key => $panel) {
                $prefix = $panel['prefix'] ?? null;

                if ($prefix && $request->is($prefix, $prefix.'/*') && Route::has("{$key}.login")) {
                    return route("{$key}.login");
                }
            }

            return route('login');
        });

        // The mirror case: an already-authenticated visitor opening a guest
        // page (e.g. a signed-in admin hitting /admin/login) lands on the
        // dashboard of the panel they were in, not the site root.
        $middleware->redirectUsersTo(function (Request $request): string {
            foreach (config('panels', []) as $key => $panel) {
                $prefix = $panel['prefix'] ?? null;

                if ($prefix && $request->is($prefix, $prefix.'/*') && Route::has("{$key}.dashboard")) {
                    return route("{$key}.dashboard");
                }
            }

            return Route::has('user.dashboard') ? route('user.dashboard') : '/';
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

$app->useLangPath(resource_path('lang'));

// App runs root-based: the web root (index.php, assets/, uploads) is one
// level above /system, so point the public path there.
$app->usePublicPath(dirname(__DIR__, 2));

return $app;
