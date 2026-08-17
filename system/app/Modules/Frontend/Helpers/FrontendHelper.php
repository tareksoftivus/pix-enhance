<?php

use App\Modules\Frontend\Models\FrontendSection;
use App\Modules\Frontend\Services\ActiveThemeResolver;
use Illuminate\Support\Str;

if (! function_exists('frontend_active_theme')) {
    function frontend_active_theme(): string
    {
        return app(ActiveThemeResolver::class)->resolve();
    }
}

if (! function_exists('frontend_section_defaults')) {
    function frontend_section_defaults(string $type): array
    {
        return collect(config("frontend-sections.{$type}.fields", []))
            ->mapWithKeys(fn (array $field, string $key): array => [$key => $field['default'] ?? null])
            ->filter(fn (mixed $value): bool => $value !== null)
            ->all();
    }
}

if (! function_exists('frontend_section_data')) {
    function frontend_section_data(?FrontendSection $section, array $fallback = []): array
    {
        if (! $section) {
            return $fallback;
        }

        $legacyDefaults = [
            'hero' => [
                'eyebrow' => 'Modern frontend experience',
                'title' => 'Launch pages that feel intentional.',
                'subtitle' => 'Build once, switch themes later, and keep the editing experience simple for your team.',
                'primary_button_text' => 'Get Started',
                'primary_button_link' => '#',
                'secondary_button_text' => 'See Features',
                'secondary_button_link' => '#features',
            ],
            'cta' => [
                'title' => 'Ready to ship faster?',
                'body' => 'Use the new frontend management stack to keep theme changes safe and predictable.',
                'button_text' => 'Contact Sales',
                'button_link' => '#contact',
            ],
            'faq' => [
                'title' => 'Frequently Asked Questions',
            ],
        ];

        $data = is_array($section->data) ? $section->data : [];

        foreach ($legacyDefaults[$section->type] ?? [] as $key => $legacyValue) {
            if (($data[$key] ?? null) === $legacyValue) {
                unset($data[$key]);
            }
        }

        if ($section->type === 'features' && is_array($data['items'] ?? null)) {
            $items = array_values($data['items']);
            $legacyLeadItem = $items[0] ?? [];

            if (($legacyLeadItem['title'] ?? null) === 'True detail upscaling up to 16K') {
                $data['lead_title'] ??= $legacyLeadItem['title'] ?? null;
                $data['lead_description'] ??= $legacyLeadItem['description'] ?? null;
                $data['items'] = array_slice($items, 1);
            }

            $data['items'] = array_values(array_filter(
                $data['items'],
                fn (array $item): bool => ($item['title'] ?? null) !== 'Batch-ready workflows'
            ));
        }

        if ($section->type === 'how_it_works') {
            $legacyIntro = [
                'badge_text' => 'Workflow',
                'title' => 'Three steps from low-res to launch-ready',
                'subtitle' => 'Upload an image, let the AI route the work, then export exactly what your channel needs.',
            ];

            foreach ($legacyIntro as $key => $legacyValue) {
                if (($data[$key] ?? null) === $legacyValue) {
                    unset($data[$key]);
                }
            }

            $legacyStepIcons = [
                'ph ph-cloud-arrow-up' => 'cloud-upload',
                'ph ph-brain' => 'brain',
                'ph ph-magic-wand' => 'wand-sparkles',
                'ph ph-download-simple' => 'download',
            ];
            $legacyDescriptions = [
                'Detail is reconstructed on dedicated GPUs - typically 2.4 seconds for a 4x upscale, with live progress.' => 'Detail is reconstructed on dedicated GPUs — typically 2.4 seconds for a 4× upscale, with live progress.',
                'Grab a lossless PNG, TIFF or optimised WEBP - or pull it straight from the API into your own product.' => 'Grab a lossless PNG, TIFF or optimised WEBP — or pull it straight from the API into your own product.',
            ];

            if (is_array($data['items'] ?? null)) {
                $data['items'] = array_values(array_map(function (array $item) use ($legacyStepIcons, $legacyDescriptions): array {
                    if (isset($legacyStepIcons[$item['icon'] ?? ''])) {
                        $item['icon'] = $legacyStepIcons[$item['icon']];
                    }

                    if (isset($legacyDescriptions[$item['description'] ?? ''])) {
                        $item['description'] = $legacyDescriptions[$item['description']];
                    }

                    unset($item['label'], $item['image']);

                    return $item;
                }, $data['items']));
            }

            if (is_array($data['stats'] ?? null)) {
                $data['stats'] = array_values(array_map(function (array $stat, int $index): array {
                    $stat['compact'] ??= $index === 0;
                    $stat['gradient'] ??= $index === 0;

                    return $stat;
                }, $data['stats'], array_keys($data['stats'])));
            }
        }

        if ($section->type === 'quality') {
            $legacyIntro = [
                'badge_text' => 'Quality check',
                'title' => 'See what real detail recovery looks like',
                'subtitle' => 'Compare original files against AI-enhanced outputs before you commit credits to a whole batch.',
            ];

            foreach ($legacyIntro as $key => $legacyValue) {
                if (($data[$key] ?? null) === $legacyValue) {
                    unset($data[$key]);
                }
            }
        }

        if ($section->type === 'ai_features') {
            $legacyIntro = [
                'badge_text' => 'AI model stack',
                'title' => 'Nine specialist models, one simple upload',
                'subtitle' => 'PixEnhance analyzes every image and routes it through the right enhancement path automatically.',
            ];

            foreach ($legacyIntro as $key => $legacyValue) {
                if (($data[$key] ?? null) === $legacyValue) {
                    unset($data[$key]);
                }
            }

            $defaultTabs = collect(frontend_section_defaults('ai_features')['items'] ?? [])
                ->keyBy('key')
                ->all();
            $legacyIcons = [
                'ph ph-arrows-out' => 'maximize-2',
                'ph ph-user-focus' => 'scan-face',
                'ph ph-eraser' => 'eraser',
                'ph ph-palette' => 'palette',
            ];
            $legacyDescriptions = [
                'A latent diffusion upscaler fine-tuned on 40M photographs. It hallucinates plausible micro-texture guided by the original edges, which is why fabric still looks like fabric at 16x.' => 'A latent diffusion upscaler fine-tuned on 40M photographs. It hallucinates plausible micro-texture guided by the original edges, which is why fabric still looks like fabric at 16×.',
                'Detects every face in the frame, restores it independently at native crop resolution, then blends it back with an identity-similarity guard.' => 'Detects every face in the frame, restores it independently at native crop resolution, then blends it back. An identity-similarity guard rejects any result that drifts from the original person.',
                'Runs before upscaling so compression damage is never magnified. Handles sensor noise, JPEG blocking, chroma bleed and mild motion blur.' => 'Runs before upscaling so compression damage is never magnified. Handles sensor noise, JPEG blocking, chroma bleed and mild motion blur in a single forward pass.',
                'Recovers clipped highlights, lifts crushed shadows and neutralises colour casts using a scene-aware tone curve.' => 'Recovers clipped highlights, lifts crushed shadows and neutralises colour casts using a scene-aware tone curve — no sliders, no presets, no muddy HDR look.',
            ];

            if (is_array($data['items'] ?? null)) {
                $data['items'] = array_values(array_map(function (array $item, int $index) use ($defaultTabs, $legacyIcons, $legacyDescriptions): array {
                    $key = Str::slug($item['key'] ?? $item['title'] ?? 'tab-'.$index);
                    $item = array_replace($defaultTabs[$key] ?? [], $item);
                    $item['key'] = $key;

                    if (isset($legacyIcons[$item['icon'] ?? ''])) {
                        $item['icon'] = $legacyIcons[$item['icon']];
                    }

                    if (isset($legacyDescriptions[$item['description'] ?? ''])) {
                        $item['description'] = $legacyDescriptions[$item['description']];
                    }

                    $item['badge_variant'] ??= $index === 0 ? 'primary' : (($item['badge'] ?? '') === 'New' ? 'success' : 'default');

                    return $item;
                }, $data['items'], array_keys($data['items'])));
            }
        }

        if ($section->type === 'faq' && is_array($data['items'] ?? null)) {
            $legacyAnswers = [
                'Photoshop doubles resolution with a general-purpose model. PixEnhance runs a detection pass first, then routes your image through the specific models it needs - face restoration, denoise, edge recovery - at up to 16x instead of 2x.' => 'Photoshop doubles resolution with a general-purpose model. PixEnhance runs a detection pass first, then routes your image through the specific models it needs — face restoration, denoise, edge recovery — at up to 16× instead of 2×.',
                'One credit = one finished image, at any scale factor. Re-running the same source with different settings costs another credit, but downloading a result you already generated is always free.' => 'One credit = one finished image, at any scale factor. Re-running the same source with different settings costs another credit, but downloading a result you already generated is always free. Credits on paid plans roll over and never expire while your subscription is active.',
                'You do - on every plan, including the free tier. Paid plans add an explicit commercial licence.' => 'You do — on every plan, including the free tier. Paid plans add an explicit commercial licence. We never train on customer uploads, and source files are permanently deleted after 24 hours unless you keep them in your library.',
                'One authenticated POST with your image URL or binary, and a webhook when the job finishes. Official SDKs cover PHP, Node and Python.' => 'One authenticated POST with your image URL or binary, and a webhook when the job finishes. Official SDKs cover PHP (Laravel-ready), Node and Python. Most teams ship their integration in an afternoon.',
                'Nothing breaks. You can buy a top-up pack at any time, or enable auto-recharge so pipelines never stall.' => 'Nothing breaks. You can buy a top-up pack at any time, or enable auto-recharge so pipelines never stall. There are no overage surprises — we only charge what you explicitly approve.',
                'Any time, from your billing page. Upgrades apply instantly with a prorated charge; downgrades take effect at the end of the current period.' => 'Any time, from your billing page. Upgrades apply instantly with a prorated charge; downgrades take effect at the end of the current period. The first 14 days are fully refundable, no questions asked.',
            ];

            $data['items'] = array_values(array_map(function (array $item) use ($legacyAnswers): array {
                if (isset($legacyAnswers[$item['answer'] ?? ''])) {
                    $item['answer'] = $legacyAnswers[$item['answer']];
                }

                return $item;
            }, $data['items']));
        }

        if ($section->type === 'cta') {
            if (($data['title'] ?? null) === 'Your next image deserves every pixel') {
                $data['title'] = 'Your next image deserves';
            }

            if (($data['body'] ?? null) === 'Upload a photo you had already given up on. If PixEnhance does not beat what you have now, you have lost ninety seconds.') {
                unset($data['body']);
            }
        }

        if ($section->type === 'pricing') {
            if (($data['title'] ?? null) === 'Pay for pixels, not seats') {
                $data['title'] = 'Pay for pixels, not';
            }

            if (($data['billing_save_label'] ?? null) === 'Save more') {
                unset($data['billing_save_label']);
            }
        }

        if ($section->type === 'pricing_plans') {
            if (($data['title'] ?? null) === 'Pay for pixels, not seats') {
                $data['title'] = 'Pay for pixels, not';
                $data['title_highlight'] ??= 'seats';
            }

            if (($data['billing_save_label'] ?? null) === 'Save more') {
                unset($data['billing_save_label']);
            }
        }

        if ($section->type === 'pricing_hero' && ($data['title'] ?? null) === 'Pricing that scales with your image pipeline.') {
            $data['title'] = 'Pricing that scales with your';
            $data['title_highlight'] ??= 'image pipeline';
            $data['title_suffix'] ??= '.';
        }

        if ($section->type === 'pricing_compare' && ($data['title'] ?? null) === 'Choose by volume, not by missing features.') {
            $data['title'] = 'Choose by volume, not by';
            $data['title_highlight'] ??= 'missing features';
            $data['title_suffix'] ??= '.';
        }

        if ($section->type === 'features_hero' && ($data['title'] ?? null) === 'Features built for every image workflow.') {
            $data['title'] = 'Features built for every';
            $data['title_highlight'] ??= 'image workflow';
            $data['title_suffix'] ??= '.';
        }

        if ($section->type === 'features_overview' && ($data['title'] ?? null) === 'One workspace for cleanup, scale and delivery.') {
            $data['title'] = 'One workspace for cleanup,';
            $data['title_highlight'] ??= 'scale and delivery';
            $data['title_suffix'] ??= '.';
        }

        if ($section->type === 'features_ai' && ($data['title'] ?? null) === 'The right model runs before you have to choose it.') {
            $data['title'] = 'The right model runs before';
            $data['title_highlight'] ??= 'you have to choose it';
            $data['title_suffix'] ??= '.';
        }

        if ($section->type === 'docs_hero' && ($data['title'] ?? null) === 'PixEnhance Docs') {
            $data['title'] = 'PixEnhance';
            $data['title_highlight'] ??= 'Docs';
        }

        if ($section->type === 'blog_hero' && ($data['title'] ?? null) === 'Better images, fewer reshoots.') {
            $data['title'] = 'Better images,';
            $data['title_highlight'] ??= 'fewer reshoots';
            $data['title_suffix'] ??= '.';
        }

        if ($section->type === 'testimonials') {
            if (($data['badge_text'] ?? null) === '5.0 average from image teams') {
                unset($data['badge_text']);
            }

            if (($data['subtitle'] ?? null) === 'Photographers, e-commerce teams and archivists who care about the pixel level - and noticed the difference immediately.') {
                unset($data['subtitle']);
            }
        }

        return array_replace($fallback, frontend_section_defaults($section->type), $data);
    }
}

if (! function_exists('frontend_theme_asset')) {
    function frontend_theme_asset(?string $path, string $theme = 'enhance'): ?string
    {
        if (! $path) {
            return null;
        }

        $path = trim($path);

        if (preg_match('#^(https?:)?//#', $path) || str_starts_with($path, '/') || str_starts_with($path, 'data:')) {
            return $path;
        }

        if (str_starts_with($path, 'assets/')) {
            return asset($path);
        }

        return asset('assets/frontend/'.$theme.'/'.ltrim($path, '/'));
    }
}
