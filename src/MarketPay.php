<?php

namespace Jramhani\LaravelMarketPay;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Arr;

/**
 * MarketPay API Integration
 * @see https://docs.prod.mpg.market-pay.com/api
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
     * @see https://docs.prod.mpg.market-pay.com/api#authentication
     */
    protected function getAccessToken()
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
            throw new \Exception('Failed to get access token: ' . $e->getMessage());
        }
    }

    protected function request(string $method, string $endpoint, array $options = [])
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
            throw new \Exception('API request failed: ' . $e->getMessage());
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
     * @see https://docs.prod.mpg.market-pay.com/api#create-natural-user
     */
    public function createUser(array $data)
    {
        return $this->request('POST', '/v2.01/User/natural', [
            'json' => $data
        ]);
    }

    /**
     * Create a new wallet
     * @see https://docs.prod.mpg.market-pay.com/api#create-wallet
     */
    public function createWallet(array $data)
    {
        return $this->request('POST', '/v2.01/Wallets', [
            'json' => $data
        ]);
    }

    /**
     * Create a direct card PayIn
     * @see https://docs.prod.mpg.market-pay.com/api#create-direct-card-payin
     */
    public function createPayIn(array $data)
    {
        return $this->request('POST', '/v2.01/PayIns/card/direct', [
            'json' => $data
        ]);
    }

    /**
     * Create a PayOut (withdrawal to bank account)
     * @see https://docs.prod.mpg.market-pay.com/api#create-payout
     */
    public function createPayOut(array $data)
    {
        return $this->request('POST', '/v2.01/PayOuts', [
            'json' => $data
        ]);
    }

    /**
     * Create a transfer between wallets
     * @see https://docs.prod.mpg.market-pay.com/api#create-transfer
     */
    public function createTransfer(array $data)
    {
        return $this->request('POST', '/v2.01/Transfers', [
            'json' => $data
        ]);
    }
} 