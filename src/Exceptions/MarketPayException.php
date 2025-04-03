<?php

namespace Jramhani\LaravelMarketPay\Exceptions;

class MarketPayException extends \Exception
{
    protected ?string $errorType;
    protected ?array $errors;

    public function __construct(string $message, ?string $errorType = null, ?array $errors = null, int $code = 0)
    {
        parent::__construct($message, $code);
        $this->errorType = $errorType;
        $this->errors = $errors;
    }

    public function getErrorType(): ?string
    {
        return $this->errorType;
    }

    public function getErrors(): ?array
    {
        return $this->errors;
    }
} 