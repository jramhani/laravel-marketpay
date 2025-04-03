<?php

namespace Jramhani\LaravelMarketPay\DTOs;

class PayIn extends BaseDTO
{
    public ?string $Id = null;
    public string $AuthorId;
    public string $CreditedWalletId;
    public Money $DebitedFunds;
    public Money $Fees;
    public ?string $CardId = null;
    public ?string $SecureModeReturnURL = null;
    public ?string $StatementDescriptor = null;
    public ?string $Culture = 'EN';
    public ?string $Status = null;
    public ?string $ResultCode = null;
    public ?string $ResultMessage = null;
    public ?string $ExecutionDate = null;
    public ?string $Type = 'CARD';
    public ?string $Nature = 'REGULAR';
    public ?string $PaymentType = 'CARD';
    public ?string $ExecutionType = 'DIRECT';
} 