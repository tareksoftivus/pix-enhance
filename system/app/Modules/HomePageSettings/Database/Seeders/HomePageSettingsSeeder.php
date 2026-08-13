<?php

namespace App\Modules\HomePageSettings\Database\Seeders;

use App\Modules\HomePageSettings\Models\HomePageSetting;
use Illuminate\Database\Seeder;

class HomePageSettingsSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('home-page-settings', []) as $group) {
            foreach ($group['settings'] as $key => $definition) {
                $default = $definition['default'] ?? null;

                $stored = match ($definition['type'] ?? 'text') {
                    'boolean', 'feature' => $default ? '1' : '0',
                    default => $default !== null ? (string) $default : null,
                };

                HomePageSetting::firstOrCreate(
                    ['key' => $key],
                    ['value' => $stored]
                );
            }
        }
    }
}
