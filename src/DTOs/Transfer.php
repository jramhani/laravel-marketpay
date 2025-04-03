<?php

namespace Jramhani\LaravelMarketPay\DTOs;

class Transfer extends BaseDTO
{
    public ?string $Id = null;
    public string $AuthorId;
    public string $CreditedUserId;
    public string $DebitedFundsUserId;
    public string $CreditedWalletId;
    public string $DebitedWalletId;
    public Money $DebitedFunds;
    public Money $Fees;
    public ?string $Status = null;
    public ?string $ResultCode = null;
    public ?string $ResultMessage = null;
    public ?string $ExecutionDate = null;
    public ?string $Type = 'TRANSFER';
    public ?string $Nature = 'REGULAR';
} 