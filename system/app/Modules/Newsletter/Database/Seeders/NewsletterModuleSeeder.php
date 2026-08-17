<?php

namespace App\Modules\Newsletter\Database\Seeders;

use App\Modules\Newsletter\Models\Subscriber;
use Illuminate\Database\Seeder;

class NewsletterModuleSeeder extends Seeder
{
    public function run(): void
    {
        $emails = [
            'content@northwind.example',
            'ops@orbitly.example',
            'studio@pixelforge.example',
            'archive@lumen.example',
        ];

        foreach ($emails as $email) {
            Subscriber::firstOrCreate(
                ['email' => $email],
                [
                    'active' => true,
                ]
            );
        }
    }
}
