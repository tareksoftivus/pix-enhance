<?php

namespace App\Modules\Shared\Traits;

use Illuminate\Support\Facades\URL;

/**
 * Snapshot the live request host when a mailable/notification is constructed so
 * that links resolve to the real domain even when the message is rendered later
 * in a queue worker (which has no incoming request). Without this, off-request
 * route()/url() calls fall back to config('app.url').
 */
trait ResolvesRealHost
{
    protected ?string $resolvedRootUrl = null;

    /**
     * Capture the current base URL. Call from the constructor while a request is
     * still available; the value is serialized with the queued job.
     */
    protected function captureRootUrl(): void
    {
        $this->resolvedRootUrl = real_host();
    }

    /**
     * Bind URL generation to the captured host for the duration of rendering.
     */
    protected function bindRootUrl(): void
    {
        if ($this->resolvedRootUrl === null || $this->resolvedRootUrl === '') {
            return;
        }

        URL::forceRootUrl($this->resolvedRootUrl);

        if (str_starts_with($this->resolvedRootUrl, 'https://')) {
            URL::forceScheme('https');
        }
    }
}
