<?php

namespace App\Modules\Frontend\Database\Seeders;

use App\Modules\Frontend\Models\FrontendThemeSetting;
use Illuminate\Database\Seeder;

class FrontendThemeSettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'active_theme' => 'classic',
            'theme.classic.enabled' => '1',
            'theme.classic.logo_text' => 'Classic Horizon',
            'theme.classic.primary_color' => '#D97706',
            'theme.classic.accent_color' => '#1F2937',
            'theme.classic.show_hero_kicker' => '1',
            'theme.studio.enabled' => '1',
            'theme.studio.logo_text' => 'Studio Pulse',
            'theme.studio.primary_color' => '#0F766E',
            'theme.studio.accent_color' => '#111827',
            'theme.studio.uppercase_headings' => '0',
        ];

        foreach ($defaults as $key => $value) {
            FrontendThemeSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}
