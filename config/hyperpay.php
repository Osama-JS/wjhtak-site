<?php

return [
    'base_url' => env('HYPERPAY_BASE_URL', 'https://eu-test.oppwa.com/v1/'),
    'access_token' => env('HYPERPAY_ACCESS_TOKEN', 'OGFjN2E0Yzc5NDgzMDkyNjAxOTQ4MzY2MzY1ZDAxMTZ8NnpwP1Q9Y3dGTiUyYWN6NmFmQWo='),

    'entity_ids' => [
        'mada' => env('HYPERPAY_ENTITY_ID_MADA', '8ac7a4c79483092601948366b9d1011b'),
        'visa_master' => env('HYPERPAY_ENTITY_ID_VISA_MASTER', '8ac7a4c79483092601948366b9d1011b'),
        'apple_pay' => env('HYPERPAY_ENTITY_ID_APPLE'),
    ],

    'currency' => env('HYPERPAY_CURRENCY', 'SAR'),

    // Test mode: when true, adds testMode=EXTERNAL and 3DS2_enrolled=true
    'test_mode' => env('HYPERPAY_TEST_MODE', true),
];
