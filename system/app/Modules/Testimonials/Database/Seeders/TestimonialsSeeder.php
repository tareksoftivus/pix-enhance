<?php

namespace App\Modules\Testimonials\Database\Seeders;

use App\Modules\Testimonials\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialsSeeder extends Seeder
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function definitions(): array
    {
        return [
            [
                'client_name' => 'Marta Kovac',
                'company_name' => 'Northwind',
                'designation' => 'Head of Content',
                'quote' => 'We reshot an entire catalogue because the originals were 1200px. PixEnhance would have saved us $40,000 and three weeks.',
                'rating' => 5,
                'sort_order' => 1,
            ],
            [
                'client_name' => 'Priya Raghunathan',
                'company_name' => 'Orbitly',
                'designation' => 'Staff Engineer',
                'quote' => 'We push 12,000 listing photos a day through the API. Latency stays under three seconds and failed jobs are rare enough to notice.',
                'rating' => 5,
                'sort_order' => 2,
            ],
            [
                'client_name' => 'Jonas Lindqvist',
                'company_name' => 'PixelForge',
                'designation' => 'Creative Director',
                'quote' => 'Print clients ask for 300 DPI on files shot for Instagram. PixEnhance turns an impossible request into a two-minute job.',
                'rating' => 5,
                'sort_order' => 3,
            ],
            [
                'client_name' => 'Daniel Okafor',
                'company_name' => 'Lumen Archive',
                'designation' => 'Photo Restorer',
                'quote' => 'I restore family archives professionally. The face model does not invent a new person. It brings back who was already there.',
                'rating' => 5,
                'sort_order' => 4,
            ],
            [
                'client_name' => 'Amara Diallo',
                'company_name' => 'Studio Nimbus',
                'designation' => 'Founder',
                'quote' => 'The background removal handles curly hair without a single manual mask. That is the detail that made our editors switch.',
                'rating' => 5,
                'sort_order' => 5,
            ],
            [
                'client_name' => 'Tomas Herrera',
                'company_name' => 'VertexLab',
                'designation' => 'CTO',
                'quote' => 'We switched from a self-hosted ESRGAN setup, deleted 900 lines of queue code, and cut our GPU bill by sixty percent.',
                'rating' => 5,
                'sort_order' => 6,
            ],
            [
                'client_name' => 'Evan Brooks',
                'company_name' => 'AtlasCo',
                'designation' => 'Marketplace Ops Lead',
                'quote' => 'Our sellers upload every kind of product shot imaginable. PixEnhance normalizes the output without slowing the publishing workflow.',
                'rating' => 5,
                'sort_order' => 7,
            ],
            [
                'client_name' => 'Nadia Rahman',
                'company_name' => 'Framehouse',
                'designation' => 'Production Manager',
                'quote' => 'Batch enhancement used to be the last manual step before export. Now our team checks the proofs instead of waiting on resize jobs.',
                'rating' => 4,
                'sort_order' => 8,
            ],
            [
                'client_name' => 'Leah Martin',
                'company_name' => 'Printline',
                'designation' => 'Art Director',
                'quote' => 'The upscaler gives us client-ready detail from files we used to reject. It has quietly rescued more campaigns than I can count.',
                'rating' => 5,
                'sort_order' => 9,
            ],
        ];
    }

    public function run(): void
    {
        $avatars = [
            'assets/frontend/enhance/img/avatars/avatar-1.svg',
            'assets/frontend/enhance/img/avatars/avatar-3.svg',
            'assets/frontend/enhance/img/avatars/avatar-4.svg',
            'assets/frontend/enhance/img/avatars/avatar-2.svg',
            'assets/frontend/enhance/img/avatars/avatar-5.svg',
            'assets/frontend/enhance/img/avatars/avatar-6.svg',
        ];

        foreach (self::definitions() as $i => $definition) {
            Testimonial::query()->updateOrCreate(
                ['client_name' => $definition['client_name']],
                [
                    'company_name' => $definition['company_name'],
                    'designation' => $definition['designation'],
                    'quote' => $definition['quote'],
                    'rating' => $definition['rating'],
                    'sort_order' => $definition['sort_order'],
                    'active' => true,
                    'avatar' => $avatars[$i % count($avatars)],
                ]
            );
        }
    }
}
