<?php

namespace Jramhani\LaravelMarketPay;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Arr;
use Jramhani\LaravelMarketPay\DTOs\NaturalUser;
use Jramhani\LaravelMarketPay\DTOs\Money;
use Jramhani\LaravelMarketPay\DTOs\BankAccount;
use Jramhani\LaravelMarketPay\DTOs\Card;
use Jramhani\LaravelMarketPay\DTOs\Wallet;
use Jramhani\LaravelMarketPay\DTOs\PayIn;
use Jramhani\LaravelMarketPay\DTOs\PayOut;
use Jramhani\LaravelMarketPay\DTOs\Transfer;
use Jramhani\LaravelMarketPay\DTOs\Refund;
use Jramhani\LaravelMarketPay\Exceptions\MarketPayException;
use Jramhani\LaravelMarketPay\Exceptions\AuthenticationException;
use Jramhani\LaravelMarketPay\Exceptions\ValidationException;

/**
 * MarketPay API Integration
 * @see https://docs.prod.mpg.market-pay.com/api
 * @see https://api.marketpay.io/api-docs/index.html
 */
class MarketPay
{
    protected Client $client;
    protected array $config;
    protected ?string $accessToken = null;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->initializeClient();
    }

    /**
     * Initialize the HTTP client
     */
    protected function initializeClient(): void
    {
        $this->client = new Client([
            'base_uri' => $this->getBaseUrl(),
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Set the HTTP client (for testing purposes)
     */
    public function setClient(Client $client): void
    {
        $this->client = $client;
    }

    /**
     * Get the HTTP client instance
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    protected function getBaseUrl(): string
    {
        $environment = $this->config['environment'];
        return $this->config['base_url'][$environment];
    }

    /**
     * Get OAuth access token
     * @throws AuthenticationException
     */
    protected function getAccessToken(): string
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        try {
            $response = $this->client->post('/oauth/token', [
                'json' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->config['client_id'],
                    'client_secret' => $this->config['client_secret'],
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            $this->accessToken = $data['access_token'];
            return $this->accessToken;
        } catch (GuzzleException $e) {
            throw new AuthenticationException('Failed to get access token: ' . $e->getMessage());
        }
    }

    /**
     * Make an API request
     * @throws MarketPayException|AuthenticationException|ValidationException
     */
    protected function request(string $method, string $endpoint, array $options = []): array
    {
        try {
            $token = $this->getAccessToken();
            
            $options = array_merge_recursive($options, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                ],
            ]);

            $response = $this->client->request($method, $endpoint, $options);
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            $response = $e->hasResponse() ? json_decode($e->getResponse()->getBody()->getContents(), true) : null;
            
            if ($e->getCode() === 401) {
                throw new AuthenticationException(
                    $response['Message'] ?? 'Authentication failed',
                    $response['errors'] ?? null
                );
            }

            if ($e->getCode() === 422) {
                throw new ValidationException(
                    $response['Message'] ?? 'Validation failed',
                    $response['errors'] ?? null
                );
            }

            throw new MarketPayException(
                $response['Message'] ?? $e->getMessage(),
                $response['Type'] ?? null,
                $response['errors'] ?? null,
                $e->getCode()
            );
        }
    }

    /**
     * Register a new card for a user
     * @see https://docs.prod.mpg.market-pay.com/api#card-registration
     */
    public function createCardRegistration(array $data)
    {
        return $this->request('POST', '/v2.01/CardRegistrations/card/register', [
            'json' => $data
        ]);
    }

    /**
     * Create a natural user
     * @throws MarketPayException|AuthenticationException|ValidationException
     */
    public function createUser(array $data): array
    {
        $user = new NaturalUser($data);
        return $this->request('POST', '/v2.01/User/natural', [
            'json' => $user->toArray()
        ]);
    }

    /**
     * Get a user by ID
     * @see https://api.marketpay.io/api-docs/index.html#operation/getUserById
     */
    public function getUser(string $userId): array
    {
        return $this->request('GET', "/v2.01/User/{$userId}");
    }

    /**
     * Update a natural user
     * @see https://api.marketpay.io/api-docs/index.html#operation/updateNaturalUser
     */
    public function updateUser(string $userId, array $data): array
    {
        $user = new NaturalUser($data);
        return $this->request('PUT', "/v2.01/User/natural/{$userId}", [
            'json' => $user->toArray()
        ]);
    }

    /**
     * List all users
     * @see https://api.marketpay.io/api-docs/index.html#operation/getUsers
     */
    public function listUsers(array $filters = []): array
    {
        return $this->request('GET', '/v2.01/User', [
            'query' => $filters
        ]);
    }

    /**
     * Create a bank account for a user
     * @throws MarketPayException|AuthenticationException|ValidationException
     */
    public function createBankAccount(string $userId, array $data): array
    {
        $bankAccount = new BankAccount($data);
        $bankAccount->UserId = $userId;
        
        return $this->request('POST', "/v2.01/User/{$userId}/bankaccounts/iban", [
            'json' => $bankAccount->toArray()
        ]);
    }

    /**
     * Get a bank account
     * @see https://api.marketpay.io/api-docs/index.html#operation/getBankAccount
     */
    public function getBankAccount(string $userId, string $bankAccountId): array
    {
        return $this->request('GET', "/v2.01/User/{$userId}/bankaccounts/{$bankAccountId}");
    }

    /**
     * List bank accounts for a user
     * @see https://api.marketpay.io/api-docs/index.html#operation/getBankAccounts
     */
    public function listBankAccounts(string $userId): array
    {
        return $this->request('GET', "/v2.01/User/{$userId}/bankaccounts");
    }

    /**
     * Deactivate a bank account
     * @see https://api.marketpay.io/api-docs/index.html#operation/deactivateBankAccount
     */
    public function deactivateBankAccount(string $userId, string $bankAccountId): array
    {
        return $this->request('PUT', "/v2.01/User/{$userId}/bankaccounts/{$bankAccountId}");
    }

    /**
     * Get card details
     * @see https://api.marketpay.io/api-docs/index.html#operation/getCard
     */
    public function getCard(string $cardId): array
    {
        return $this->request('GET', "/v2.01/Cards/{$cardId}");
    }

    /**
     * List cards for a user
     * @see https://api.marketpay.io/api-docs/index.html#operation/getCards
     */
    public function listCards(string $userId): array
    {
        return $this->request('GET', "/v2.01/User/{$userId}/cards");
    }

    /**
     * Deactivate a card
     * @see https://api.marketpay.io/api-docs/index.html#operation/deactivateCard
     */
    public function deactivateCard(string $cardId): array
    {
        return $this->request('PUT', "/v2.01/Cards/{$cardId}");
    }

    /**
     * Get a wallet
     * @see https://api.marketpay.io/api-docs/index.html#operation/getWallet
     */
    public function getWallet(string $walletId): array
    {
        return $this->request('GET', "/v2.01/Wallets/{$walletId}");
    }

    /**
     * List transactions for a wallet
     * @see https://api.marketpay.io/api-docs/index.html#operation/getWalletTransactions
     */
    public function listWalletTransactions(string $walletId, array $filters = []): array
    {
        return $this->request('GET', "/v2.01/Wallets/{$walletId}/transactions", [
            'query' => $filters
        ]);
    }

    /**
     * Get a PayIn
     * @see https://api.marketpay.io/api-docs/index.html#operation/getPayIn
     */
    public function getPayIn(string $payInId): array
    {
        return $this->request('GET', "/v2.01/PayIns/{$payInId}");
    }

    /**
     * Get a PayOut
     * @see https://api.marketpay.io/api-docs/index.html#operation/getPayOut
     */
    public function getPayOut(string $payOutId): array
    {
        return $this->request('GET', "/v2.01/PayOuts/{$payOutId}");
    }

    /**
     * Get a Transfer
     * @see https://api.marketpay.io/api-docs/index.html#operation/getTransfer
     */
    public function getTransfer(string $transferId): array
    {
        return $this->request('GET', "/v2.01/Transfers/{$transferId}");
    }

    /**
     * Get a Refund
     * @see https://api.marketpay.io/api-docs/index.html#operation/getRefund
     */
    public function getRefund(string $refundId): array
    {
        return $this->request('GET', "/v2.01/Refunds/{$refundId}");
    }

    /**
     * Create a Refund for a PayIn
     * @throws MarketPayException|AuthenticationException|ValidationException
     */
    public function createPayInRefund(string $payInId, array $data): array
    {
        $refund = new Refund($data);
        return $this->request('POST', "/v2.01/PayIns/{$payInId}/refunds", [
            'json' => $refund->toArray()
        ]);
    }

    /**
     * Create a Refund for a Transfer
     * @throws MarketPayException|AuthenticationException|ValidationException
     */
    public function createTransferRefund(string $transferId, array $data): array
    {
        $refund = new Refund($data);
        return $this->request('POST', "/v2.01/Transfers/{$transferId}/refunds", [
            'json' => $refund->toArray()
        ]);
    }

    /**
     * Create a new wallet
     * @throws MarketPayException|AuthenticationException|ValidationException
     */
    public function createWallet(array $data): array
    {
        $wallet = new Wallet($data);
        return $this->request('POST', '/v2.01/Wallets', [
            'json' => $wallet->toArray()
        ]);
    }

    /**
     * Create a direct card PayIn
     * @throws MarketPayException|AuthenticationException|ValidationException
     */
    public function createPayIn(array $data): array
    {
        $payIn = new PayIn($data);
        return $this->request('POST', '/v2.01/PayIns/card/direct', [
            'json' => $payIn->toArray()
        ]);
    }

    /**
     * Create a PayOut (withdrawal to bank account)
     * @throws MarketPayException|AuthenticationException|ValidationException
     */
    public function createPayOut(array $data): array
    {
        $payOut = new PayOut($data);
        return $this->request('POST', '/v2.01/PayOuts', [
            'json' => $payOut->toArray()
        ]);
    }

    /**
     * Create a transfer between wallets
     * @throws MarketPayException|AuthenticationException|ValidationException
     */
    public function createTransfer(array $data): array
    {
        $transfer = new Transfer($data);
        return $this->request('POST', '/v2.01/Transfers', [
            'json' => $transfer->toArray()
        ]);
    }

    /**
     * Test connection to the API
     * @see https://docs.prod.mpg.market-pay.com/api/#ping
     * @throws MarketPayException
     */
    public function ping(): bool
    {
        try {
            $response = $this->client->get('/ping/sys');
            return $response->getStatusCode() === 200;
        } catch (GuzzleException $e) {
            throw new MarketPayException('API is not accessible: ' . $e->getMessage());
        }
    }
} 