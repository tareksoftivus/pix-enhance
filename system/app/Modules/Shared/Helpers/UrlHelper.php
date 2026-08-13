<?php

use Illuminate\Support\Facades\Cache;

if (! function_exists('remember_real_host')) {
    /**
     * Persist the given base URL as the last known real host. Called from the
     * HTTP middleware on every web request so off-request contexts (queue
     * workers, scheduler, artisan) can resolve the same domain later.
     */
    function remember_real_host(string $root): void
    {
        Cache::forever('app.real_host', $root);
    }
}

if (! function_exists('real_host')) {
    /**
     * Resolve the application's real base URL (scheme://host[:port]) without a
     * hard-coded APP_URL.
     *
     * Inside an HTTP request the incoming host is authoritative. Off-request
     * (queued mail, scheduler) it falls back to the host cached during the last
     * web request, then to config('app.url') as a final safety net. This lets
     * the boilerplate ship without a mandatory APP_URL: links track whatever
     * domain the app is actually served from.
     */
    function real_host(): string
    {
        if (! app()->runningInConsole() && app()->bound('request')) {
            return request()->getSchemeAndHttpHost().request()->getBaseUrl();
        }

        $cached = Cache::get('app.real_host');

        if (is_string($cached) && $cached !== '') {
            return $cached;
        }

        return rtrim((string) config('app.url', 'http://localhost'), '/');
    }
}
