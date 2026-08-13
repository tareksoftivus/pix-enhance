<?php

namespace Database\Seeders;

use App\Modules\AiSettings\Database\Seeders\AiSettingSeeder;
use App\Modules\Currencies\Database\Seeders\CurrencySeeder;
use App\Modules\Frontend\Database\Seeders\FrontendMenuSeeder;
use App\Modules\Frontend\Database\Seeders\FrontendPageSeeder;
use App\Modules\Frontend\Database\Seeders\FrontendSectionSeeder;
use App\Modules\Frontend\Database\Seeders\FrontendThemeSettingSeeder;
use App\Modules\Languages\Database\Seeders\LanguagesSeeder;
use App\Modules\NotificationTemplates\Database\Seeders\NotificationTemplateSeeder;
use App\Modules\PaymentGatewaySettings\Database\Seeders\PaymentGatewaySettingsSeeder;
use App\Modules\Settings\Database\Seeders\BrandMediaSeeder;
use App\Modules\Settings\Database\Seeders\SettingSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            AdminUserSeeder::class,
            SettingSeeder::class,
            BrandMediaSeeder::class,
            LanguagesSeeder::class,
            NotificationTemplateSeeder::class,
            PaymentGatewaySettingsSeeder::class,
            AiSettingSeeder::class,
            CurrencySeeder::class,
            FrontendThemeSettingSeeder::class,
            FrontendSectionSeeder::class,
            FrontendPageSeeder::class,
            FrontendMenuSeeder::class,

            // Demo/presentation data. Order matters: users first, then the
            // records that reference them. Each is idempotent (safe to re-run).
            DemoUsersSeeder::class,
            DemoPaymentsSeeder::class,
            DemoSupportTicketsSeeder::class,
            DemoActivityLogsSeeder::class,
            DemoBlogSeeder::class,
        ]);
    }
}
