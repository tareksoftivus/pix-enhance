<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ImpersonationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ImpersonationController extends Controller
{
    public function __construct(
        protected ImpersonationService $impersonationService,
    ) {}

    /**
     * Start impersonating a website user and return the user dashboard URL.
     *
     * The admin panel opens this URL in a new tab, where the request is now
     * authenticated as the impersonated user on the web guard.
     */
    public function start(Request $request, User $user): JsonResponse
    {
        $this->impersonationService->startForUser($request->user(), $user, $request);

        return response()->json([
            'url' => route('user.dashboard'),
        ]);
    }

    /**
     * Stop impersonating and return to original admin account.
     */
    public function stop(Request $request): RedirectResponse
    {
        if (! $this->impersonationService->isImpersonating($request)) {
            return redirect('/');
        }

        $this->impersonationService->stop($request);

        return redirect()->route('admin.dashboard')
            ->with('success', __('Impersonation ended. Welcome back!'));
    }
}
