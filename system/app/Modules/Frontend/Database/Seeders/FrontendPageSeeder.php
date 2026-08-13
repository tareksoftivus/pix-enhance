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

        $homeSectionIds = FrontendSection::whereIn('slug', [
            'homepage-hero',
            'homepage-features',
            'homepage-testimonials',
            'global-footer',
        ])->pluck('id')->all();

        $aboutSectionIds = FrontendSection::whereIn('slug', [
            'about-rich-content',
            'global-footer',
        ])->pluck('id')->all();

        $composer->syncSections($home, $homeSectionIds);
        $composer->syncSections($about, $aboutSectionIds);
    }
}
