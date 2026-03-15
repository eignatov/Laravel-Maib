<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | maib e-Commerce API Credentials
    |--------------------------------------------------------------------------
    | Project ID and Secret are available in maibmerchants.md after
    | activating your project. Used to generate access tokens.
    */
    'project_id' => env('MAIB_PROJECT_ID'),
    'project_secret' => env('MAIB_PROJECT_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Signature Key
    |--------------------------------------------------------------------------
    | Used to validate the HMAC-SHA256 signature on callback notifications.
    | Available in maibmerchants.md under your project settings.
    */
    'signature_key' => env('MAIB_SIGNATURE_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Environment
    |--------------------------------------------------------------------------
    | 'production' → https://api.maibmerchants.md/v1
    | 'sandbox'    → same URL, uses test project credentials
    */
    'environment' => env('MAIB_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | API Base URL
    |--------------------------------------------------------------------------
    */
    'base_url' => 'https://api.maibmerchants.md/v1',

    /*
    |--------------------------------------------------------------------------
    | Callback Route
    |--------------------------------------------------------------------------
    | The URI where maib ecomm will POST transaction notifications.
    | Must be publicly accessible and excluded from CSRF middleware.
    | Register this URL in your maibmerchants.md project settings.
    */
    'callback_route' => env('MAIB_CALLBACK_ROUTE', '/maib/callback'),

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    | Driver and prefix used to store access/refresh tokens.
    */
    'cache_driver' => env('MAIB_CACHE_DRIVER', null),
    'cache_prefix' => 'maib_token',
];
