<?php

namespace App\Modules\Frontend\Database\Seeders;

use App\Modules\Frontend\Models\FrontendSection;
use App\Modules\Frontend\Models\Page;
use App\Modules\Frontend\Services\PageComposerService;
use Illuminate\Database\Seeder;

class FrontendPageSeeder extends Seeder
{
    public function run(): void
    {
        /** @var PageComposerService $composer */
        $composer = app(PageComposerService::class);

        $home = Page::updateOrCreate(
            ['slug' => 'home'],
            [
                'title' => 'Home',
                'status' => 'published',
                'excerpt' => 'Homepage powered by the frontend management stack.',
                'default_layout' => 'landing',
                'theme_overrides' => [],
                'is_system' => true,
                'is_home' => true,
                'meta_title' => 'Home',
                'meta_description' => 'Homepage for the admin-panel frontend stack.',
                'meta_image_media_id' => null,
                'published_at' => now(),
            ]
        );

        $about = Page::updateOrCreate(
            ['slug' => 'about'],
            [
                'title' => 'About',
                'status' => 'published',
                'excerpt' => 'About page powered by shared theme-aware content.',
                'default_layout' => 'default',
                'theme_overrides' => [],
                'is_system' => false,
                'is_home' => false,
                'meta_title' => 'About',
                'meta_description' => 'About the multi-theme frontend stack.',
                'meta_image_media_id' => null,
                'published_at' => now(),
            ]
        );

        $pricing = Page::updateOrCreate(
            ['slug' => 'pricing'],
            [
                'title' => 'Pricing',
                'status' => 'published',
                'excerpt' => 'Simple credit pricing for AI image enhancement.',
                'default_layout' => 'default',
                'theme_overrides' => [],
                'is_system' => false,
                'is_home' => false,
                'meta_title' => 'Pricing',
                'meta_description' => 'Simple credit pricing for AI image upscaling, restoration and enhancement.',
                'meta_image_media_id' => null,
                'published_at' => now(),
            ]
        );

        $features = Page::updateOrCreate(
            ['slug' => 'features'],
            [
                'title' => 'Features',
                'status' => 'published',
                'excerpt' => 'AI enhancement features for every image workflow.',
                'default_layout' => 'default',
                'theme_overrides' => [],
                'is_system' => false,
                'is_home' => false,
                'meta_title' => 'Features',
                'meta_description' => 'Explore the AI image enhancement, upscaling, restoration and batch workflow features in Enhance.',
                'meta_image_media_id' => null,
                'published_at' => now(),
            ]
        );

        $docs = Page::updateOrCreate(
            ['slug' => 'docs'],
            [
                'title' => 'Documentation',
                'status' => 'published',
                'excerpt' => 'Guides for getting reliable results from PixEnhance.',
                'default_layout' => 'default',
                'theme_overrides' => [],
                'is_system' => false,
                'is_home' => false,
                'meta_title' => 'Documentation',
                'meta_description' => 'Learn how to upload, enhance, upscale and export images with PixEnhance.',
                'meta_image_media_id' => null,
                'published_at' => now(),
            ]
        );

        $terms = Page::updateOrCreate(
            ['slug' => 'terms-conditions'],
            [
                'title' => 'Terms & Conditions',
                'status' => 'published',
                'excerpt' => 'Terms for using PixEnhance.',
                'default_layout' => 'default',
                'theme_overrides' => [],
                'is_system' => false,
                'is_home' => false,
                'meta_title' => 'Terms & Conditions',
                'meta_description' => 'Read the PixEnhance terms and conditions for account, billing and platform use.',
                'meta_image_media_id' => null,
                'published_at' => now(),
            ]
        );

        $privacy = Page::updateOrCreate(
            ['slug' => 'privacy-policy'],
            [
                'title' => 'Privacy Policy',
                'status' => 'published',
                'excerpt' => 'Privacy practices for PixEnhance.',
                'default_layout' => 'default',
                'theme_overrides' => [],
                'is_system' => false,
                'is_home' => false,
                'meta_title' => 'Privacy Policy',
                'meta_description' => 'Learn how PixEnhance collects, uses and protects account, usage and image-processing data.',
                'meta_image_media_id' => null,
                'published_at' => now(),
            ]
        );

        $cookie = Page::updateOrCreate(
            ['slug' => 'cookie-policy'],
            [
                'title' => 'Cookie Policy',
                'status' => 'published',
                'excerpt' => 'Cookie practices for PixEnhance.',
                'default_layout' => 'default',
                'theme_overrides' => [],
                'is_system' => false,
                'is_home' => false,
                'meta_title' => 'Cookie Policy',
                'meta_description' => 'Learn how PixEnhance uses cookies and similar technologies for security, preferences and analytics.',
                'meta_image_media_id' => null,
                'published_at' => now(),
            ]
        );

        $homeSectionSlugs = [
            'homepage-hero',
            'homepage-logos',
            'homepage-features',
            'homepage-how-it-works',
            'homepage-quality',
            'homepage-ai-features',
            'homepage-pricing',
            'homepage-testimonials',
            'homepage-faq',
            'homepage-cta',
        ];

        $homeSections = FrontendSection::whereIn('slug', $homeSectionSlugs)->pluck('id', 'slug');
        $homeSectionIds = collect($homeSectionSlugs)->map(fn (string $slug) => $homeSections[$slug] ?? null)->all();

        $aboutSectionSlugs = [
            'about-rich-content',
        ];

        $aboutSections = FrontendSection::whereIn('slug', $aboutSectionSlugs)->pluck('id', 'slug');
        $aboutSectionIds = collect($aboutSectionSlugs)->map(fn (string $slug) => $aboutSections[$slug] ?? null)->all();

        $pricingSectionSlugs = [
            'pricing-hero',
            'pricing-plans',
            'pricing-compare',
        ];

        $pricingSections = FrontendSection::whereIn('slug', $pricingSectionSlugs)->pluck('id', 'slug');
        $pricingSectionIds = collect($pricingSectionSlugs)->map(fn (string $slug) => $pricingSections[$slug] ?? null)->all();

        $featuresSectionSlugs = [
            'features-hero',
            'features-overview',
            'features-ai',
        ];

        $featuresSections = FrontendSection::whereIn('slug', $featuresSectionSlugs)->pluck('id', 'slug');
        $featuresSectionIds = collect($featuresSectionSlugs)->map(fn (string $slug) => $featuresSections[$slug] ?? null)->all();

        $docsSectionSlugs = [
            'docs-hero',
            'docs-content',
        ];

        $docsSections = FrontendSection::whereIn('slug', $docsSectionSlugs)->pluck('id', 'slug');
        $docsSectionIds = collect($docsSectionSlugs)->map(fn (string $slug) => $docsSections[$slug] ?? null)->all();

        $termsSectionSlugs = [
            'terms-hero',
            'terms-content',
        ];

        $termsSections = FrontendSection::whereIn('slug', $termsSectionSlugs)->pluck('id', 'slug');
        $termsSectionIds = collect($termsSectionSlugs)->map(fn (string $slug) => $termsSections[$slug] ?? null)->all();

        $privacySectionSlugs = [
            'privacy-hero',
            'privacy-content',
        ];

        $privacySections = FrontendSection::whereIn('slug', $privacySectionSlugs)->pluck('id', 'slug');
        $privacySectionIds = collect($privacySectionSlugs)->map(fn (string $slug) => $privacySections[$slug] ?? null)->all();

        $cookieSectionSlugs = [
            'cookie-hero',
            'cookie-content',
        ];

        $cookieSections = FrontendSection::whereIn('slug', $cookieSectionSlugs)->pluck('id', 'slug');
        $cookieSectionIds = collect($cookieSectionSlugs)->map(fn (string $slug) => $cookieSections[$slug] ?? null)->all();

        $composer->syncSections($home, $homeSectionIds);
        $composer->syncSections($about, $aboutSectionIds);
        $composer->syncSections($pricing, $pricingSectionIds);
        $composer->syncSections($features, $featuresSectionIds);
        $composer->syncSections($docs, $docsSectionIds);
        $composer->syncSections($terms, $termsSectionIds);
        $composer->syncSections($privacy, $privacySectionIds);
        $composer->syncSections($cookie, $cookieSectionIds);
    }
}
