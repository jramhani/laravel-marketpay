<?php

namespace Jramhani\LaravelMarketPay\DTOs;

class PayOut extends BaseDTO
{
    public ?string $Id = null;
    public string $AuthorId;
    public string $DebitedWalletId;
    public string $BankAccountId;
    public Money $DebitedFunds;
    public Money $Fees;
    public ?string $BankWireRef = null;
    public ?string $Status = null;
    public ?string $ResultCode = null;
    public ?string $ResultMessage = null;
    public ?string $ExecutionDate = null;
    public ?string $Type = 'PAYOUT';
    public ?string $Nature = 'REGULAR';
    public ?string $PaymentType = 'BANK_WIRE';
} 