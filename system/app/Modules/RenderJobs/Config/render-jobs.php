<?php

return [
    'disk' => env('RENDER_JOBS_DISK', 'public'),

    'sync_processing' => env('RENDER_JOBS_SYNC', true),

    'max_upload_kb' => 51200,

    'max_local_output_pixels' => 12000000,

    'output_formats' => ['png', 'jpg', 'webp'],

    'tools' => [
        'upscaler' => [
            'label' => 'Upscaler',
            'icon' => 'maximize-2',
            'scale_costs' => [
                2 => 1,
                4 => 2,
                8 => 4,
                16 => 8,
            ],
            'allowed_scales' => [2, 4, 8, 16],
            'stages' => [
                'Reading the source',
                'Removing compression artefacts',
                'Reconstructing texture',
                'Upscaling tiles',
                'Stitching and writing the output',
            ],
        ],
        'face-restoration' => [
            'label' => 'Face restoration',
            'icon' => 'scan-face',
            'base_cost' => 2,
            'scale_costs' => [
                1 => 2,
                2 => 2,
                4 => 3,
            ],
            'allowed_scales' => [1, 2, 4],
            'stages' => [
                'Detecting faces',
                'Aligning facial landmarks',
                'Rebuilding facial detail',
                'Blending restored areas',
                'Writing the output',
            ],
        ],
        'background-removal' => [
            'label' => 'Background removal',
            'icon' => 'eraser',
            'fixed_cost' => 1,
            'allowed_scales' => [1],
            'no_scale' => true,
            'stages' => [
                'Detecting the subject',
                'Tracing the boundary',
                'Refining edges',
                'Compositing the output',
                'Writing the transparent file',
            ],
        ],
    ],
];
