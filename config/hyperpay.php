<?php

return [
    'base_url' => env('HYPERPAY_BASE_URL', 'https://test.oppwa.com/v1/'),
    'access_token' => env('HYPERPAY_ACCESS_TOKEN'),

    'entity_ids' => [
        'mada' => env('HYPERPAY_ENTITY_ID_MADA'),
        'visa_master' => env('HYPERPAY_ENTITY_ID_VISA_MASTER'),
        'apple_pay' => env('HYPERPAY_ENTITY_ID_APPLE'),
    ],

    'currency' => env('HYPERPAY_CURRENCY', 'SAR'),
];
