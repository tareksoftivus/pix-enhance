<?php

namespace App\Modules\Frontend\Database\Seeders;

use App\Modules\Frontend\Models\FrontendThemeSetting;
use Illuminate\Database\Seeder;

class FrontendThemeSettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'active_theme' => 'enhance',
            'theme.enhance.enabled' => '1',
            'theme.enhance.logo_text' => 'Enhance',
            'theme.enhance.primary_color' => '#2563EB',
            'theme.enhance.accent_color' => '#111827',
            'theme.enhance.show_hero_kicker' => '1',
        ];

        foreach ($defaults as $key => $value) {
            FrontendThemeSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}
