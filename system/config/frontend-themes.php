<?php

return [
    'enhance' => [
        'key' => 'enhance',
        'label' => 'Enhance',
        'description' => 'Primary frontend theme for the Enhance application.',
        'preview_image' => null,
        'default_enabled' => true,
        'view_namespace' => 'frontend.themes.enhance',
        'supported_section_types' => [
            'hero',
            'logos',
            'features',
            'how_it_works',
            'quality',
            'ai_features',
            'pricing',
            'testimonials',
            'pricing_hero',
            'pricing_plans',
            'pricing_compare',
            'features_hero',
            'features_overview',
            'features_ai',
            'terms_hero',
            'terms_content',
            'privacy_hero',
            'privacy_content',
            'cookie_hero',
            'cookie_content',
            'faq',
            'cta',
            'rich_content',
        ],
        'page_layouts' => [
            'default' => [
                'label' => 'Default',
                'view' => 'layouts.page',
                'is_default' => true,
            ],
            'landing' => [
                'label' => 'Landing',
                'view' => 'layouts.landing',
            ],
        ],
        'fallback_renderer' => 'frontend.shared.sections.unsupported',
        'theme_settings_schema' => [
            'branding' => [
                'label' => 'Branding',
                'icon' => 'ph ph-palette',
                'description' => 'Theme-specific branding values.',
                'settings' => [
                    'logo_text' => [
                        'type' => 'text',
                        'label' => 'Logo Text',
                        'default' => 'Enhance',
                        'rules' => 'nullable|string|max:100',
                    ],
                    'primary_color' => [
                        'type' => 'color',
                        'label' => 'Primary Color',
                        'default' => '#2563EB',
                        'rules' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
                    ],
                    'accent_color' => [
                        'type' => 'color',
                        'label' => 'Accent Color',
                        'default' => '#111827',
                        'rules' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
                    ],
                    'show_hero_kicker' => [
                        'type' => 'feature',
                        'label' => 'Show Hero Kicker',
                        'default' => true,
                    ],
                ],
            ],
        ],
    ],
];
