<?php

namespace App\Modules\Frontend\Database\Seeders;

use App\Modules\Frontend\Models\FrontendSection;
use App\Modules\Frontend\Services\FrontendSectionService;
use Illuminate\Database\Seeder;

class FrontendSectionSeeder extends Seeder
{
    public function run(): void
    {
        /** @var FrontendSectionService $service */
        $service = app(FrontendSectionService::class);

        $sections = [
            [
                'name' => 'Homepage Hero',
                'slug' => 'homepage-hero',
                'type' => 'hero',
                'status' => 'published',
                'description' => 'Primary hero section for the homepage.',
                'data' => [],
            ],
            [
                'name' => 'Homepage Features',
                'slug' => 'homepage-features',
                'type' => 'feature_grid',
                'status' => 'published',
                'description' => 'Feature highlights for the homepage.',
                'data' => [],
            ],
            [
                'name' => 'Homepage Testimonials',
                'slug' => 'homepage-testimonials',
                'type' => 'testimonial_grid',
                'status' => 'published',
                'description' => 'Social proof section for the homepage.',
                'data' => [],
            ],
            [
                'name' => 'Global Footer',
                'slug' => 'global-footer',
                'type' => 'footer',
                'status' => 'published',
                'description' => 'Reusable footer content.',
                'data' => [],
            ],
            [
                'name' => 'About Content',
                'slug' => 'about-rich-content',
                'type' => 'rich_content',
                'status' => 'published',
                'description' => 'Long-form content for the about page.',
                'data' => [
                    'title' => 'About this frontend stack',
                    'content' => '<p>This shared-content frontend stack is designed so you can add more themes later without redoing your content model.</p>',
                ],
            ],
        ];

        foreach ($sections as $section) {
            FrontendSection::updateOrCreate(
                ['slug' => $section['slug']],
                [
                    'name' => $section['name'],
                    'type' => $section['type'],
                    'status' => $section['status'],
                    'description' => $section['description'],
                    'data' => $service->normalizeData($section['type'], $section['data']),
                    'theme_overrides' => [],
                    'preview_image_media_id' => null,
                ]
            );
        }
    }
}
