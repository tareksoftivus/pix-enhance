<?php

namespace App\Http\Middleware;

use App\Services\ImpersonationService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ShareImpersonation
{
    public function __construct(
        protected ImpersonationService $impersonationService,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        // Only flag impersonation on the panel where the impersonated identity
        // actually is — not on the admin panel the real admin is still using.
        $isImpersonating = $this->impersonationService->isImpersonationActive($request);
        $impersonator = $this->impersonationService->getImpersonator($request);

        view()->share('isImpersonating', $isImpersonating);
        view()->share('impersonator', $impersonator);

        return $next($request);
    }
}
