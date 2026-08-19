<?php

return [

    'general' => [
        'label' => 'General',
        'icon' => 'ph ph-sliders',
        'description' => 'Default providers used across the application.',
        'card_groups' => true,
        'settings' => [
            'ai_default_text_provider' => [
                'type' => 'select',
                'label' => 'Default text provider',
                'default' => 'gemini',
                'options' => [
                    'gemini' => 'Gemini',
                    'ollama' => 'Ollama',
                ],
                'card_group' => [
                    'label' => 'Text & Chat',
                    'icon' => 'ph ph-chat-circle-text',
                    'description' => 'Provider used for text-based AI features.',
                ],
            ],
            'ai_default_image_provider' => [
                'type' => 'select',
                'label' => 'Default image provider',
                'default' => 'gemini',
                // Ollama is intentionally excluded — it has no image editing support.
                'options' => [
                    'gemini' => 'Gemini',
                ],
                'card_group' => [
                    'label' => 'Image Editing',
                    'icon' => 'ph ph-image',
                    'description' => 'Provider used to enhance and edit images.',
                ],
            ],
        ],
    ],

    'gemini' => [
        'label' => 'Google Gemini',
        'icon' => 'ph ph-google-logo',
        'description' => 'Used for AI image editing (free tier available).',
        'settings' => [
            'gemini_enabled' => [
                'type' => 'feature',
                'label' => 'Enable Gemini',
                'default' => false,
            ],
            'gemini_api_key' => [
                'type' => 'text',
                'label' => 'API key',
                'default' => env('GEMINI_API_KEY'),
                'hint' => 'Falls back to GEMINI_API_KEY in .env if left blank.',
                'rules' => ['nullable', 'string'],
            ],
            'gemini_image_models' => [
                'type' => 'text',
                'label' => 'Enabled image models',
                'default' => 'gemini-2.5-flash-image',
                'hint' => 'Comma-separated model IDs shown in the render model picker on the user dashboard.',
            ],
        ],
    ],

    'ollama' => [
        'label' => 'Ollama',
        'icon' => 'ph ph-cube',
        'description' => 'Local models. Text-only — not used for image editing.',
        'settings' => [
            'ollama_enabled' => [
                'type' => 'feature',
                'label' => 'Enable Ollama',
                'default' => false,
            ],
            'ollama_base_url' => [
                'type' => 'text',
                'label' => 'Base URL',
                'default' => env('OLLAMA_BASE_URL', 'http://localhost:11434'),
            ],
            // No ollama_api_key setting — matches AiSettingsService::getProviderConfig()'s
            // existing "Ollama has no API key" special-case (it's a local provider).
        ],
    ],

];
