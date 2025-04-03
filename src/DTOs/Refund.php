<?php

namespace Jramhani\LaravelMarketPay\DTOs;

class Refund extends BaseDTO
{
    public ?string $Id = null;
    public string $AuthorId;
    public string $DebitedWalletId;
    public string $CreditedWalletId;
    public Money $DebitedFunds;
    public Money $Fees;
    public ?string $Status = null;
    public ?string $ResultCode = null;
    public ?string $ResultMessage = null;
    public ?string $ExecutionDate = null;
    public ?string $Type = 'REFUND';
    public ?string $Nature = 'REFUND';
    public ?string $InitialTransactionId = null;
    public ?string $InitialTransactionType = null;
    public ?string $RefundReason = null;
} 