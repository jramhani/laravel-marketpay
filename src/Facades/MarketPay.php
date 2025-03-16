<?php

namespace Jramhani\LaravelMarketPay\Facades;

use Illuminate\Support\Facades\Facade;

class MarketPay extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'marketpay';
    }
} 