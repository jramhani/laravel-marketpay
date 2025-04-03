<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MarketPay API Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for the MarketPay API integration.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | API Environment
    |--------------------------------------------------------------------------
    |
    | The environment to use for the MarketPay API.
    | Supported: "sandbox", "production"
    |
    */
    'environment' => env('MARKETPAY_ENVIRONMENT', 'sandbox'),

    /*
    |--------------------------------------------------------------------------
    | API Credentials
    |--------------------------------------------------------------------------
    |
    | Your MarketPay API credentials.
    |
    */
    'client_id' => env('MARKETPAY_CLIENT_ID'),
    'client_secret' => env('MARKETPAY_CLIENT_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | API URLs
    |--------------------------------------------------------------------------
    |
    | The base URLs for the MarketPay API.
    |
    */
    'base_url' => [
        'sandbox' => 'https://api.sandbox.marketpay.io',
        'production' => 'https://api.marketpay.io',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Currency
    |--------------------------------------------------------------------------
    |
    | The default currency to use for transactions.
    |
    */
    'default_currency' => env('MARKETPAY_DEFAULT_CURRENCY', 'EUR'),

    /*
    |--------------------------------------------------------------------------
    | Webhook Secret
    |--------------------------------------------------------------------------
    |
    | The secret key used to verify webhook signatures.
    |
    */
    'webhook_secret' => env('MARKETPAY_WEBHOOK_SECRET'),
]; 