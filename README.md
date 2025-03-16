# Laravel MarketPay

A Laravel package for easy integration with the MarketPay API.

## Installation

You can install the package via composer:

```bash
composer require jramhani/laravel-marketpay
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag="marketpay-config"
```

Add the following environment variables to your `.env` file:

```env
MARKETPAY_API_KEY=your_api_key
MARKETPAY_CLIENT_ID=your_client_id
MARKETPAY_CLIENT_SECRET=your_client_secret
MARKETPAY_ENVIRONMENT=sandbox # or production
```

## Usage

### User Management

```php
use MarketPay;

// Create a natural user
$user = MarketPay::createUser([
    'FirstName' => 'John',
    'LastName' => 'Doe',
    'Email' => 'john@example.com',
    'Birthday' => '1990-01-01',
    'Nationality' => 'FR',
    'CountryOfResidence' => 'FR'
]);

// Create a wallet for the user
$wallet = MarketPay::createWallet([
    'Owners' => [$user['Id']],
    'Description' => 'Main wallet',
    'Currency' => 'EUR'
]);
```

### Card Registration

```php
// Register a card
$cardRegistration = MarketPay::createCardRegistration([
    'UserId' => $user['Id'],
    'Currency' => 'EUR'
]);
```

### Payments

```php
// Create a PayIn (charge a card)
$payIn = MarketPay::createPayIn([
    'AuthorId' => $user['Id'],
    'CreditedWalletId' => $wallet['Id'],
    'DebitedFunds' => [
        'Currency' => 'EUR',
        'Amount' => 1000 // Amount in cents
    ],
    'Fees' => [
        'Currency' => 'EUR',
        'Amount' => 0
    ],
    'CardId' => $cardId
]);

// Create a Transfer
$transfer = MarketPay::createTransfer([
    'AuthorId' => $user['Id'],
    'DebitedWalletId' => $wallet['Id'],
    'CreditedWalletId' => $destinationWallet['Id'],
    'DebitedFunds' => [
        'Currency' => 'EUR',
        'Amount' => 1000
    ],
    'Fees' => [
        'Currency' => 'EUR',
        'Amount' => 0
    ]
]);

// Create a PayOut (withdraw to bank account)
$payOut = MarketPay::createPayOut([
    'AuthorId' => $user['Id'],
    'DebitedWalletId' => $wallet['Id'],
    'DebitedFunds' => [
        'Currency' => 'EUR',
        'Amount' => 1000
    ],
    'Fees' => [
        'Currency' => 'EUR',
        'Amount' => 0
    ],
    'BankAccountId' => $bankAccountId
]);
```

## Testing

```bash
composer test
```

## Security

If you discover any security-related issues, please email your-email@example.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information. 