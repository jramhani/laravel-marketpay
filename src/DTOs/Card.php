<?php

namespace Jramhani\LaravelMarketPay\DTOs;

class Card extends BaseDTO
{
    public ?string $Id = null;
    public string $UserId;
    public ?string $CardType = null;
    public ?string $CardProvider = null;
    public ?string $Alias = null;
    public ?string $ExpirationDate = null;
    public ?string $Currency = null;
    public ?bool $Active = true;
    public ?string $Fingerprint = null;
} 