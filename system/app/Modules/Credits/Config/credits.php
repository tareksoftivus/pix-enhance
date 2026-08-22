<?php

return [
    'signup_credits' => 10,
    'low_balance_threshold' => 10,

    'packs' => [
        'starter' => [
            'name' => 'Starter top-up',
            'credits' => 100,
            'price' => 12.00,
            'currency' => 'USD',
            'badge' => null,
        ],
        'studio' => [
            'name' => 'Studio top-up',
            'credits' => 500,
            'price' => 52.00,
            'currency' => 'USD',
            'badge' => 'Popular',
        ],
        'scale' => [
            'name' => 'Scale top-up',
            'credits' => 2000,
            'price' => 180.00,
            'currency' => 'USD',
            'badge' => null,
        ],
        'enterprise' => [
            'name' => 'Enterprise top-up',
            'credits' => 10000,
            'price' => 780.00,
            'currency' => 'USD',
            'badge' => 'Best rate',
        ],
    ],
];
