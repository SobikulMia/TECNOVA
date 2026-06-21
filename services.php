<?php

return [

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
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

    /*
    |--------------------------------------------------------------------------
    | Warehouse / Dropshipping Fulfillment API
    |--------------------------------------------------------------------------
    | Fill these in once your 3rd-party warehouse provider issues credentials.
    | App\Services\WarehouseApiService reads exclusively from this block —
    | nothing else in the codebase should reference these env vars directly.
    */
    'warehouse' => [
        'base_url' => env('WAREHOUSE_API_BASE_URL', ''),
        'api_key' => env('WAREHOUSE_API_KEY', ''),
        'timeout' => env('WAREHOUSE_API_TIMEOUT', 15),
    ],

];
