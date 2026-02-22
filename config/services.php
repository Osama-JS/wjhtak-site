<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'travelopro' => [
        'user_id' => env('TRAVELOPRO_USER_ID'),
        'password' => env('TRAVELOPRO_PASSWORD'),
        'access' => env('TRAVELOPRO_ACCESS', 'Test'),
        'url' => 'https://travelnext.works/api/aeroVE5/availability',
    ],

     'tabby' => [
        'public_key' => env('TABBY_PUBLIC_KEY'),
        'secret_key' => env('TABBY_SECRET_KEY'),
        'merchant_code' => env('TABBY_MERCHANT_CODE'),
        'base_url' => env('TABBY_BASE_URL', 'https://api.tabby.ai/api/v2'),
    ],

    'tamara' => [
        'api_token' => env('TAMARA_API_TOKEN'),
        'notification_key' => env('TAMARA_NOTIFICATION_KEY'),
        'base_url' => env('TAMARA_API_URL', 'https://api-sandbox.tamara.co'),
    ],

    'firebase' => [
        'project_id' => env('FIREBASE_PROJECT_ID'),
        'credentials_path' => env('FIREBASE_CREDENTIALS_PATH', 'storage/app/firebase/service-account.json'),
    ],

    'tap' => [
        'secret_key' => env('TAP_SECRET_KEY'),
        'public_key' => env('TAP_PUBLIC_KEY'),
        'base_url' => env('TAP_BASE_URL', 'https://api.tap.company/v2'),
    ],

];
