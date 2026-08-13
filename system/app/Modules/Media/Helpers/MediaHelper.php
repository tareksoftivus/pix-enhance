<?php

use App\Modules\Media\Models\Media;

if (! function_exists('media_url')) {
    /**
     * Get the public URL for a media record by ID.
     */
    function media_url(mixed $mediaId): ?string
    {
        if (! $mediaId) {
            return null;
        }

        $media = Media::find($mediaId);

        return $media?->url;
    }
}
