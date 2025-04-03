<?php

namespace Jramhani\LaravelMarketPay;

use Illuminate\Support\ServiceProvider;

class MarketPayServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/marketpay.php' => config_path('marketpay.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/marketpay.php',
            'marketpay'
        );

        $this->app->singleton(MarketPay::class, function ($app) {
            return new MarketPay(config('marketpay'));
        });

        $this->app->alias(MarketPay::class, 'marketpay');
    }
} 