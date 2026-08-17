<?php

namespace App\Modules\Frontend\Database\Seeders;

use App\Modules\Frontend\Models\FrontendSection;
use App\Modules\Frontend\Services\FrontendSectionService;
use App\Modules\Frontend\Services\SectionRegistry;
use Illuminate\Database\Seeder;

class FrontendSectionSeeder extends Seeder
{
    public function run(): void
    {
        /** @var FrontendSectionService $service */
        $service = app(FrontendSectionService::class);
        $sectionDefaults = fn (string $type): array => app(SectionRegistry::class)->defaults($type);

        $sections = [
            [
                'name' => 'Homepage Hero',
                'slug' => 'homepage-hero',
                'type' => 'hero',
                'status' => 'published',
                'description' => 'Primary hero section for the homepage.',
                'data' => $sectionDefaults('hero'),
            ],
            [
                'name' => 'Homepage Logos',
                'slug' => 'homepage-logos',
                'type' => 'logos',
                'status' => 'published',
                'description' => 'Trusted brand logo strip for the homepage.',
                'data' => $sectionDefaults('logos'),
            ],
            [
                'name' => 'Homepage Features',
                'slug' => 'homepage-features',
                'type' => 'features',
                'status' => 'published',
                'description' => 'Feature highlights for the homepage.',
                'data' => $sectionDefaults('features'),
            ],
            [
                'name' => 'Homepage How It Works',
                'slug' => 'homepage-how-it-works',
                'type' => 'how_it_works',
                'status' => 'published',
                'description' => 'Process steps for the homepage.',
                'data' => $sectionDefaults('how_it_works'),
            ],
            [
                'name' => 'Homepage Quality',
                'slug' => 'homepage-quality',
                'type' => 'quality',
                'status' => 'published',
                'description' => 'Quality comparison section for the homepage.',
                'data' => $sectionDefaults('quality'),
            ],
            [
                'name' => 'Homepage AI Features',
                'slug' => 'homepage-ai-features',
                'type' => 'ai_features',
                'status' => 'published',
                'description' => 'AI feature tabs for the homepage.',
                'data' => $sectionDefaults('ai_features'),
            ],
            [
                'name' => 'Homepage Pricing',
                'slug' => 'homepage-pricing',
                'type' => 'pricing',
                'status' => 'published',
                'description' => 'Pricing cards for the homepage.',
                'data' => $sectionDefaults('pricing'),
            ],
            [
                'name' => 'Homepage Testimonials',
                'slug' => 'homepage-testimonials',
                'type' => 'testimonials',
                'status' => 'published',
                'description' => 'Social proof section for the homepage.',
                'data' => $sectionDefaults('testimonials'),
            ],
            [
                'name' => 'Homepage FAQ',
                'slug' => 'homepage-faq',
                'type' => 'faq',
                'status' => 'published',
                'description' => 'Frequently asked questions for the homepage.',
                'data' => $sectionDefaults('faq'),
            ],
            [
                'name' => 'Homepage CTA',
                'slug' => 'homepage-cta',
                'type' => 'cta',
                'status' => 'published',
                'description' => 'Final call to action for the homepage.',
                'data' => $sectionDefaults('cta'),
            ],
            [
                'name' => 'Pricing Hero',
                'slug' => 'pricing-hero',
                'type' => 'pricing_hero',
                'status' => 'published',
                'description' => 'Pricing page hero section.',
                'data' => $sectionDefaults('pricing_hero'),
            ],
            [
                'name' => 'Pricing Plans',
                'slug' => 'pricing-plans',
                'type' => 'pricing_plans',
                'status' => 'published',
                'description' => 'Pricing cards for the pricing page.',
                'data' => $sectionDefaults('pricing_plans'),
            ],
            [
                'name' => 'Pricing Compare',
                'slug' => 'pricing-compare',
                'type' => 'pricing_compare',
                'status' => 'published',
                'description' => 'Feature comparison table for the pricing page.',
                'data' => $sectionDefaults('pricing_compare'),
            ],
            [
                'name' => 'Features Hero',
                'slug' => 'features-hero',
                'type' => 'features_hero',
                'status' => 'published',
                'description' => 'Features page hero section.',
                'data' => $sectionDefaults('features_hero'),
            ],
            [
                'name' => 'Features Overview',
                'slug' => 'features-overview',
                'type' => 'features_overview',
                'status' => 'published',
                'description' => 'Capability grid for the features page.',
                'data' => $sectionDefaults('features_overview'),
            ],
            [
                'name' => 'Features AI Routing',
                'slug' => 'features-ai',
                'type' => 'features_ai',
                'status' => 'published',
                'description' => 'AI routing details for the features page.',
                'data' => $sectionDefaults('features_ai'),
            ],
            [
                'name' => 'Docs Hero',
                'slug' => 'docs-hero',
                'type' => 'docs_hero',
                'status' => 'published',
                'description' => 'Documentation page hero section.',
                'data' => $sectionDefaults('docs_hero'),
            ],
            [
                'name' => 'Docs Content',
                'slug' => 'docs-content',
                'type' => 'docs_content',
                'status' => 'published',
                'description' => 'Documentation page content section.',
                'data' => $sectionDefaults('docs_content'),
            ],
            [
                'name' => 'Terms Hero',
                'slug' => 'terms-hero',
                'type' => 'terms_hero',
                'status' => 'published',
                'description' => 'Terms and conditions hero section.',
                'data' => $sectionDefaults('terms_hero'),
            ],
            [
                'name' => 'Terms Content',
                'slug' => 'terms-content',
                'type' => 'terms_content',
                'status' => 'published',
                'description' => 'Terms and conditions content section.',
                'data' => $sectionDefaults('terms_content'),
            ],
            [
                'name' => 'Privacy Hero',
                'slug' => 'privacy-hero',
                'type' => 'privacy_hero',
                'status' => 'published',
                'description' => 'Privacy policy hero section.',
                'data' => $sectionDefaults('privacy_hero'),
            ],
            [
                'name' => 'Privacy Content',
                'slug' => 'privacy-content',
                'type' => 'privacy_content',
                'status' => 'published',
                'description' => 'Privacy policy content section.',
                'data' => $sectionDefaults('privacy_content'),
            ],
            [
                'name' => 'Cookie Hero',
                'slug' => 'cookie-hero',
                'type' => 'cookie_hero',
                'status' => 'published',
                'description' => 'Cookie policy hero section.',
                'data' => $sectionDefaults('cookie_hero'),
            ],
            [
                'name' => 'Cookie Content',
                'slug' => 'cookie-content',
                'type' => 'cookie_content',
                'status' => 'published',
                'description' => 'Cookie policy content section.',
                'data' => $sectionDefaults('cookie_content'),
            ],
            [
                'name' => 'Blog Hero',
                'slug' => 'blog-hero',
                'type' => 'blog_hero',
                'status' => 'published',
                'description' => 'Blog index hero section.',
                'data' => $sectionDefaults('blog_hero'),
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
