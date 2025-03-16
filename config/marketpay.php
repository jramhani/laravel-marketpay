<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MarketPay API Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure your MarketPay API settings.
    |
    */

    'api_key' => env('MARKETPAY_API_KEY'),
    
    'client_id' => env('MARKETPAY_CLIENT_ID'),
    
    'client_secret' => env('MARKETPAY_CLIENT_SECRET'),

    'environment' => env('MARKETPAY_ENVIRONMENT', 'sandbox'),

    'base_url' => [
        'sandbox' => 'https://api.sandbox.market-pay.com',
        'production' => 'https://api.prod.market-pay.com',
    ],

    'version' => 'v1',
]; 