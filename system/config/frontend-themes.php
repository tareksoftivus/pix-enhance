<?php

return [
    'classic' => [
        'key' => 'classic',
        'label' => 'Classic Horizon',
        'description' => 'Warm editorial layout with soft cards and generous spacing.',
        'preview_image' => null,
        'default_enabled' => true,
        'view_namespace' => 'frontend.themes.classic',
        'supported_section_types' => ['hero', 'feature_grid', 'cta', 'faq', 'testimonial_grid', 'rich_content', 'footer'],
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
                'description' => 'Theme-specific branding and hero presentation.',
                'settings' => [
                    'logo_text' => [
                        'type' => 'text',
                        'label' => 'Logo Text',
                        'default' => 'Classic Horizon',
                        'rules' => 'nullable|string|max:100',
                    ],
                    'primary_color' => [
                        'type' => 'color',
                        'label' => 'Primary Color',
                        'default' => '#D97706',
                        'rules' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
                    ],
                    'accent_color' => [
                        'type' => 'color',
                        'label' => 'Accent Color',
                        'default' => '#1F2937',
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
    'studio' => [
        'key' => 'studio',
        'label' => 'Studio Pulse',
        'description' => 'Bold contemporary layout with bright contrast and cleaner grids.',
        'preview_image' => null,
        'default_enabled' => true,
        'view_namespace' => 'frontend.themes.studio',
        'supported_section_types' => ['hero', 'feature_grid', 'cta', 'faq', 'testimonial_grid', 'rich_content', 'footer'],
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
                'icon' => 'ph ph-sparkle',
                'description' => 'Color system and editorial tone for this theme.',
                'settings' => [
                    'logo_text' => [
                        'type' => 'text',
                        'label' => 'Logo Text',
                        'default' => 'Studio Pulse',
                        'rules' => 'nullable|string|max:100',
                    ],
                    'primary_color' => [
                        'type' => 'color',
                        'label' => 'Primary Color',
                        'default' => '#0F766E',
                        'rules' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
                    ],
                    'accent_color' => [
                        'type' => 'color',
                        'label' => 'Accent Color',
                        'default' => '#111827',
                        'rules' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
                    ],
                    'uppercase_headings' => [
                        'type' => 'feature',
                        'label' => 'Uppercase Headings',
                        'default' => false,
                    ],
                ],
            ],
        ],
    ],
];
