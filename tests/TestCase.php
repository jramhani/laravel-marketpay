<?php

namespace Jramhani\LaravelMarketPay\Tests;

use Jramhani\LaravelMarketPay\MarketPayServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            MarketPayServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('marketpay.api_key', 'test-api-key');
        $app['config']->set('marketpay.client_id', 'test-client-id');
        $app['config']->set('marketpay.client_secret', 'test-client-secret');
        $app['config']->set('marketpay.environment', 'sandbox');
    }
} 