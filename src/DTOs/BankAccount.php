<?php

namespace Jramhani\LaravelMarketPay\DTOs;

class BankAccount extends BaseDTO
{
    public ?string $Id = null;
    public string $OwnerName;
    public string $OwnerAddress;
    public string $IBAN;
    public string $BIC;
    public ?string $Type = 'IBAN';
    public ?string $UserId = null;
    public ?bool $Active = true;
} 