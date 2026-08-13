<?php

return [
    'hero' => [
        'type' => 'hero',
        'label' => 'Hero',
        'icon' => 'ph ph-flag-banner',
        'description' => 'Large header section with a title, message, and actions.',
        'category' => 'Marketing',
        'supported_themes' => ['classic', 'studio'],
        'fallback_renderer' => 'frontend.shared.sections.unsupported',
        'fields' => [
            'eyebrow' => [
                'type' => 'text',
                'label' => 'Eyebrow',
                'default' => 'Modern frontend experience',
                'rules' => 'nullable|string|max:120',
            ],
            'title' => [
                'type' => 'text',
                'label' => 'Title',
                'default' => 'Launch pages that feel intentional.',
                'rules' => 'required|string|max:255',
            ],
            'subtitle' => [
                'type' => 'textarea',
                'label' => 'Subtitle',
                'default' => 'Build once, switch themes later, and keep the editing experience simple for your team.',
                'rules' => 'nullable|string|max:1000',
            ],
            'primary_button_text' => [
                'type' => 'text',
                'label' => 'Primary Button Text',
                'default' => 'Get Started',
                'rules' => 'nullable|string|max:100',
            ],
            'primary_button_link' => [
                'type' => 'text',
                'label' => 'Primary Button Link',
                'default' => '#',
                'rules' => 'nullable|string|max:255',
            ],
            'secondary_button_text' => [
                'type' => 'text',
                'label' => 'Secondary Button Text',
                'default' => 'See Features',
                'rules' => 'nullable|string|max:100',
            ],
            'secondary_button_link' => [
                'type' => 'text',
                'label' => 'Secondary Button Link',
                'default' => '#features',
                'rules' => 'nullable|string|max:255',
            ],
        ],
    ],
    'feature_grid' => [
        'type' => 'feature_grid',
        'label' => 'Feature Grid',
        'icon' => 'ph ph-squares-four',
        'description' => 'Grid of feature cards with concise summaries.',
        'category' => 'Marketing',
        'supported_themes' => ['classic', 'studio'],
        'fallback_renderer' => 'frontend.shared.sections.unsupported',
        'fields' => [
            'title' => [
                'type' => 'text',
                'label' => 'Title',
                'default' => 'Everything is theme-ready from day one',
                'rules' => 'required|string|max:255',
            ],
            'subtitle' => [
                'type' => 'textarea',
                'label' => 'Subtitle',
                'default' => 'Shared content, theme-aware rendering, and cleaner workflows for developers.',
                'rules' => 'nullable|string|max:1000',
            ],
            'items' => [
                'type' => 'repeater',
                'label' => 'Features',
                'default' => [
                    ['title' => 'Shared content', 'description' => 'Edit pages once and reuse them across themes.'],
                    ['title' => 'Theme preview', 'description' => 'Preview any page in another enabled theme before switching.'],
                    ['title' => 'Registry contracts', 'description' => 'Keep themes and sections code-defined and maintainable.'],
                ],
                'rules' => 'nullable',
                'schema' => [
                    'title' => ['type' => 'text', 'label' => 'Title'],
                    'description' => ['type' => 'textarea', 'label' => 'Description'],
                ],
            ],
        ],
    ],
    'cta' => [
        'type' => 'cta',
        'label' => 'Call to Action',
        'icon' => 'ph ph-megaphone',
        'description' => 'Compact call-to-action section with one strong message.',
        'category' => 'Marketing',
        'supported_themes' => ['classic', 'studio'],
        'fallback_renderer' => 'frontend.shared.sections.unsupported',
        'fields' => [
            'title' => [
                'type' => 'text',
                'label' => 'Title',
                'default' => 'Ready to ship faster?',
                'rules' => 'required|string|max:255',
            ],
            'body' => [
                'type' => 'textarea',
                'label' => 'Body',
                'default' => 'Use the new frontend management stack to keep theme changes safe and predictable.',
                'rules' => 'nullable|string|max:1000',
            ],
            'button_text' => [
                'type' => 'text',
                'label' => 'Button Text',
                'default' => 'Contact Sales',
                'rules' => 'nullable|string|max:100',
            ],
            'button_link' => [
                'type' => 'text',
                'label' => 'Button Link',
                'default' => '#contact',
                'rules' => 'nullable|string|max:255',
            ],
        ],
    ],
    'faq' => [
        'type' => 'faq',
        'label' => 'FAQ',
        'icon' => 'ph ph-question',
        'description' => 'Question and answer list for common concerns.',
        'category' => 'Content',
        'supported_themes' => ['classic', 'studio'],
        'fallback_renderer' => 'frontend.shared.sections.unsupported',
        'fields' => [
            'title' => [
                'type' => 'text',
                'label' => 'Title',
                'default' => 'Frequently Asked Questions',
                'rules' => 'required|string|max:255',
            ],
            'items' => [
                'type' => 'repeater',
                'label' => 'Questions',
                'default' => [
                    ['question' => 'Can we add more themes later?', 'answer' => 'Yes. Themes are code-defined and can be added without changing the shared content model.'],
                    ['question' => 'Do pages have to be recreated per theme?', 'answer' => 'No. Pages and sections stay shared by default in this architecture.'],
                ],
                'rules' => 'nullable',
                'schema' => [
                    'question' => ['type' => 'text', 'label' => 'Question'],
                    'answer' => ['type' => 'textarea', 'label' => 'Answer'],
                ],
            ],
        ],
    ],
    'testimonial_grid' => [
        'type' => 'testimonial_grid',
        'label' => 'Testimonials',
        'icon' => 'ph ph-chat-centered-dots',
        'description' => 'Customer quotes with names and roles.',
        'category' => 'Marketing',
        'supported_themes' => ['classic', 'studio'],
        'fallback_renderer' => 'frontend.shared.sections.unsupported',
        'fields' => [
            'title' => [
                'type' => 'text',
                'label' => 'Title',
                'default' => 'Trusted by teams shipping faster',
                'rules' => 'required|string|max:255',
            ],
            'items' => [
                'type' => 'repeater',
                'label' => 'Testimonials',
                'default' => [
                    ['quote' => 'We finally have a page system that feels safe to scale.', 'name' => 'Ariana Shah', 'role' => 'Product Lead'],
                    ['quote' => 'Theme switching stopped being scary once the content model was shared.', 'name' => 'Muntasir Kabir', 'role' => 'Engineering Manager'],
                ],
                'rules' => 'nullable',
                'schema' => [
                    'quote' => ['type' => 'textarea', 'label' => 'Quote'],
                    'name' => ['type' => 'text', 'label' => 'Name'],
                    'role' => ['type' => 'text', 'label' => 'Role'],
                ],
            ],
        ],
    ],
    'rich_content' => [
        'type' => 'rich_content',
        'label' => 'Rich Content',
        'icon' => 'ph ph-text-align-left',
        'description' => 'Rich text block for long-form content pages.',
        'category' => 'Content',
        'supported_themes' => ['classic', 'studio'],
        'fallback_renderer' => 'frontend.shared.sections.unsupported',
        'fields' => [
            'title' => [
                'type' => 'text',
                'label' => 'Title',
                'default' => 'Rich content section',
                'rules' => 'nullable|string|max:255',
            ],
            'content' => [
                'type' => 'editor',
                'label' => 'Content',
                'default' => '<p>Use this section for legal pages, long descriptions, and policy content.</p>',
                'rules' => 'nullable|string',
            ],
        ],
    ],
    'footer' => [
        'type' => 'footer',
        'label' => 'Footer',
        'icon' => 'ph ph-layout',
        'description' => 'Footer content with a short statement and navigation links.',
        'category' => 'Global',
        'supported_themes' => ['classic', 'studio'],
        'fallback_renderer' => 'frontend.shared.sections.unsupported',
        'fields' => [
            'title' => [
                'type' => 'text',
                'label' => 'Title',
                'default' => 'Build with confidence',
                'rules' => 'nullable|string|max:255',
            ],
            'body' => [
                'type' => 'textarea',
                'label' => 'Body',
                'default' => 'The frontend management stack keeps content stable while themes evolve.',
                'rules' => 'nullable|string|max:1000',
            ],
            'links' => [
                'type' => 'repeater',
                'label' => 'Links',
                'default' => [
                    ['label' => 'Home', 'url' => '/'],
                    ['label' => 'About', 'url' => '/about'],
                ],
                'rules' => 'nullable',
                'schema' => [
                    'label' => ['type' => 'text', 'label' => 'Label'],
                    'url' => ['type' => 'text', 'label' => 'URL'],
                ],
            ],
        ],
    ],
];
