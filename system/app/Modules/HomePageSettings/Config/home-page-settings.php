<?php

return [
    'general' => [
        'label' => 'General',
        'icon' => 'ph ph-sliders-horizontal',
        'description' => 'Core home-page-settings configuration',
        'settings' => [
            'example_key' => [
                'type' => 'text',
                'label' => 'Example Setting',
                'hint' => 'A helpful description',
                'default' => '',
                'rules' => 'nullable|string|max:255',
            ],
        ],
    ],
];
