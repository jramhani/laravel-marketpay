<?php

namespace Jramhani\LaravelMarketPay\Exceptions;

class AuthenticationException extends MarketPayException
{
    public function __construct(string $message = 'Authentication failed', ?array $errors = null)
    {
        parent::__construct($message, 'authentication_error', $errors, 401);
    }
} 