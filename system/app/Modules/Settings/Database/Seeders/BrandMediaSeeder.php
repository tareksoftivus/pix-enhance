<?php

namespace App\Modules\Settings\Database\Seeders;

use App\Modules\Media\Models\Media;
use App\Modules\Settings\Services\SettingsService;
use Illuminate\Database\Seeder;

/**
 * Registers the shipped default logo and favicon as Media records and points
 * the site_logo / site_favicon settings at them, so the media pickers show the
 * current branding out of the box. Idempotent, and never overrides a logo an
 * admin has already chosen.
 */
class BrandMediaSeeder extends Seeder
{
    public function run(): void
    {
        $settings = app(SettingsService::class);

        $defaults = [
            'site_logo' => [
                'path' => 'brand/softivus-logo.png',
                'name' => 'Default Logo',
                'mime_type' => 'image/png',
                'extension' => 'png',
            ],
            'site_favicon' => [
                'path' => 'brand/favicon.png',
                'name' => 'Default Favicon',
                'mime_type' => 'image/png',
                'extension' => 'png',
            ],
        ];

        foreach ($defaults as $settingKey => $meta) {
            $absolute = public_path('assets/uploads/'.$meta['path']);

            if (! is_file($absolute)) {
                continue;
            }

            $media = Media::firstOrCreate(
                ['disk' => 'public', 'path' => $meta['path']],
                [
                    'name' => $meta['name'],
                    'file_name' => basename($meta['path']),
                    'original_name' => basename($meta['path']),
                    'mime_type' => $meta['mime_type'],
                    'extension' => $meta['extension'],
                    'type' => 'image',
                    'size' => filesize($absolute) ?: 0,
                ]
            );

            // Only point the setting at the default when the admin hasn't set one.
            if (! $settings->get($settingKey)) {
                $settings->set($settingKey, $media->id);
            }
        }
    }
}
