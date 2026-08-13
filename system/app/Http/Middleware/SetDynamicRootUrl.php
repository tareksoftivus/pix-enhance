<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetDynamicRootUrl
{
    /**
     * Bind URL generation to the live request host so links (redirects, SEO,
     * sitemap) track the real domain instead of a hard-coded APP_URL. The base
     * path is included so subfolder installs (example.com/my-app) keep working.
     * The root is also cached by real_host() for off-request contexts such as
     * queued mail.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $root = $request->getSchemeAndHttpHost().$request->getBaseUrl();

        remember_real_host($root);

        URL::forceRootUrl($root);

        if ($request->getScheme() === 'https') {
            URL::forceScheme('https');
        }

        // The public disk serves uploads with a root-relative URL; prefix the
        // base path so images resolve inside subfolder installs too. Cloud
        // providers (s3/r2) configure their own absolute URL — leave those.
        if (config('filesystems.disks.public.driver') === 'local' && $request->getBaseUrl() !== '') {
            config(['filesystems.disks.public.url' => $request->getBaseUrl().'/assets/uploads']);
        }

        return $next($request);
    }
}
