<?php

namespace Jramhani\LaravelMarketPay;

use Illuminate\Support\ServiceProvider;
use Jramhani\LaravelMarketPay\MarketPay;

class MarketPayServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/marketpay.php', 'marketpay'
        );

        $this->app->singleton('marketpay', function ($app) {
            return new MarketPay(config('marketpay'));
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/marketpay.php' => config_path('marketpay.php'),
        ], 'marketpay-config');
    }
} 