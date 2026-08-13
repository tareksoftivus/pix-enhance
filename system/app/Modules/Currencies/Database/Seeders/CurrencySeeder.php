<?php

namespace App\Modules\Currencies\Database\Seeders;

use App\Modules\Currencies\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'exchange_rate' => 1.000000, 'sort_order' => 1],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'exchange_rate' => 0.920000, 'sort_order' => 2],
            ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£', 'exchange_rate' => 0.790000, 'sort_order' => 3],
            ['code' => 'BDT', 'name' => 'Bangladeshi Taka', 'symbol' => '৳', 'exchange_rate' => 110.000000, 'sort_order' => 4],
            ['code' => 'INR', 'name' => 'Indian Rupee', 'symbol' => '₹', 'exchange_rate' => 83.000000, 'sort_order' => 5],
            ['code' => 'NGN', 'name' => 'Nigerian Naira', 'symbol' => '₦', 'exchange_rate' => 1550.000000, 'sort_order' => 6],
            ['code' => 'GHS', 'name' => 'Ghanaian Cedi', 'symbol' => '₵', 'exchange_rate' => 15.500000, 'sort_order' => 7],
            ['code' => 'KES', 'name' => 'Kenyan Shilling', 'symbol' => 'KSh', 'exchange_rate' => 153.000000, 'sort_order' => 8],
            ['code' => 'ZAR', 'name' => 'South African Rand', 'symbol' => 'R', 'exchange_rate' => 18.500000, 'sort_order' => 9],
            ['code' => 'CAD', 'name' => 'Canadian Dollar', 'symbol' => 'C$', 'exchange_rate' => 1.360000, 'sort_order' => 10],
            ['code' => 'AUD', 'name' => 'Australian Dollar', 'symbol' => 'A$', 'exchange_rate' => 1.530000, 'sort_order' => 11],
            ['code' => 'JPY', 'name' => 'Japanese Yen', 'symbol' => '¥', 'exchange_rate' => 151.000000, 'sort_order' => 12],
            ['code' => 'CNY', 'name' => 'Chinese Yuan', 'symbol' => '¥', 'exchange_rate' => 7.240000, 'sort_order' => 13],
            ['code' => 'MYR', 'name' => 'Malaysian Ringgit', 'symbol' => 'RM', 'exchange_rate' => 4.720000, 'sort_order' => 14],
            ['code' => 'SGD', 'name' => 'Singapore Dollar', 'symbol' => 'S$', 'exchange_rate' => 1.350000, 'sort_order' => 15],
        ];

        foreach ($currencies as $currency) {
            Currency::firstOrCreate(
                ['code' => $currency['code']],
                $currency
            );
        }
    }
}
