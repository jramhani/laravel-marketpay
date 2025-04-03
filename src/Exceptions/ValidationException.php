<?php

namespace Jramhani\LaravelMarketPay\Exceptions;

class ValidationException extends MarketPayException
{
    public function __construct(string $message = 'Validation failed', ?array $errors = null)
    {
        parent::__construct($message, 'validation_error', $errors, 422);
    }
} 