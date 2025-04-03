<?php

namespace Jramhani\LaravelMarketPay\DTOs;

class NaturalUser extends BaseDTO
{
    public ?string $Id = null;
    public string $FirstName;
    public string $LastName;
    public string $Email;
    public string $Birthday;
    public string $Nationality;
    public string $CountryOfResidence;
    public ?string $Occupation = null;
    public ?string $IncomeRange = null;
    public ?string $ProofOfIdentity = null;
    public ?string $ProofOfAddress = null;
    public ?string $PersonType = 'NATURAL';
    public ?string $KYCLevel = null;
} 