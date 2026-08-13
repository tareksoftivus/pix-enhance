<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\User;
use App\Modules\LoginActivity\Services\LoginActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonationService
{
    public function __construct(
        protected LoginActivityService $loginActivityService,
    ) {}

    /**
     * Start impersonating another admin (same guard).
     */
    public function start(Admin $impersonator, Admin $target, Request $request): void
    {
        $this->rememberImpersonator($request, $impersonator, targetGuard: 'admin');

        $this->loginActivityService->recordImpersonateStart(
            Admin::class,
            $impersonator->id,
            $request,
            [
                'impersonated_user_id' => $target->id,
                'impersonated_user_name' => $target->name,
                'impersonated_user_email' => $target->email,
            ],
        );

        Auth::guard('admin')->login($target);
    }

    /**
     * Start impersonating a website user from the admin panel.
     *
     * The admin's own session on the `admin` guard is left intact; we simply
     * log the impersonated user into the `web` guard so the user panel treats
     * the request as that user. Stopping logs the web guard back out.
     */
    public function startForUser(Admin $impersonator, User $target, Request $request): void
    {
        $this->rememberImpersonator($request, $impersonator, targetGuard: 'web');

        $this->loginActivityService->recordImpersonateStart(
            Admin::class,
            $impersonator->id,
            $request,
            [
                'impersonated_user_id' => $target->id,
                'impersonated_user_name' => $target->name,
                'impersonated_user_email' => $target->email,
            ],
        );

        Auth::guard('web')->login($target);
    }

    /**
     * Stop impersonating and return to the original admin.
     */
    public function stop(Request $request): void
    {
        $adminId = $request->session()->get('impersonating_from_id');
        $targetGuard = $request->session()->get('impersonating_target_guard', 'admin');

        $impersonatedUser = Auth::guard($targetGuard)->user();

        if ($adminId) {
            $this->loginActivityService->recordImpersonateStop(
                Admin::class,
                $adminId,
                $request,
                [
                    'impersonated_user_id' => $impersonatedUser?->id,
                    'impersonated_user_name' => $impersonatedUser?->name,
                ],
            );
        }

        if ($targetGuard === 'web') {
            // The admin never left the admin guard; just drop the user session.
            Auth::guard('web')->logout();
        } elseif ($adminId) {
            $admin = Admin::find($adminId);
            if ($admin) {
                Auth::guard('admin')->login($admin);
            }
        }

        $request->session()->forget([
            'impersonating_from_id',
            'impersonating_from_name',
            'impersonating_from_guard',
            'impersonating_target_guard',
        ]);
    }

    /**
     * Check if the current request is an impersonation session.
     */
    public function isImpersonating(Request $request): bool
    {
        return $request->session()->has('impersonating_from_id');
    }

    /**
     * Whether the impersonation banner should show for the current request.
     *
     * Impersonating a website user logs into the `web` guard while the admin's
     * own `admin` guard session stays intact — so the session flag alone is set
     * regardless of panel. The banner must only appear on the panel served by
     * the impersonation's target guard (where the impersonated identity is),
     * and only while that guard is actually authenticated.
     */
    public function isImpersonationActive(Request $request): bool
    {
        if (! $this->isImpersonating($request)) {
            return false;
        }

        $targetGuard = $request->session()->get('impersonating_target_guard', 'admin');

        return $this->panelGuardForRequest($request) === $targetGuard
            && Auth::guard($targetGuard)->check();
    }

    /**
     * Resolve the auth guard of the panel serving the current request, based on
     * its URL prefix (e.g. /admin → admin guard, /dashboard → web guard).
     */
    protected function panelGuardForRequest(Request $request): ?string
    {
        $prefix = $request->segment(1);

        foreach (config('panels', []) as $panel) {
            if (($panel['prefix'] ?? null) === $prefix) {
                return $panel['guard'] ?? 'web';
            }
        }

        return null;
    }

    /**
     * Get the original admin's info.
     *
     * @return array{id: int, name: string, guard: string}|null
     */
    public function getImpersonator(Request $request): ?array
    {
        if (! $this->isImpersonating($request)) {
            return null;
        }

        return [
            'id' => $request->session()->get('impersonating_from_id'),
            'name' => $request->session()->get('impersonating_from_name'),
            'guard' => $request->session()->get('impersonating_from_guard'),
        ];
    }

    /**
     * Persist the origin admin and the guard being impersonated into the session.
     */
    protected function rememberImpersonator(Request $request, Admin $impersonator, string $targetGuard): void
    {
        $request->session()->put('impersonating_from_id', $impersonator->id);
        $request->session()->put('impersonating_from_name', $impersonator->name);
        $request->session()->put('impersonating_from_guard', 'admin');
        $request->session()->put('impersonating_target_guard', $targetGuard);
    }
}
