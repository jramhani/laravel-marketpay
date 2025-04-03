<?php

namespace Jramhani\LaravelMarketPay\DTOs;

class Wallet extends BaseDTO
{
    public ?string $Id = null;
    public array $Owners;
    public string $Description;
    public string $Currency;
    public ?Money $Balance = null;
    public ?string $FundsType = 'DEFAULT';
} 