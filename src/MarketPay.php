<?php

namespace Jramhani\LaravelMarketPay;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Arr;

class MarketPay
{
    protected Client $client;
    protected array $config;
    protected ?string $accessToken = null;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->client = new Client([
            'base_uri' => $this->getBaseUrl(),
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    protected function getBaseUrl(): string
    {
        $environment = $this->config['environment'];
        return $this->config['base_url'][$environment];
    }

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

    // Card Registration Methods
    public function createCardRegistration(array $data)
    {
        return $this->request('POST', '/v2.01/CardRegistrations/card/register', [
            'json' => $data
        ]);
    }

    // User Methods
    public function createUser(array $data)
    {
        return $this->request('POST', '/v2.01/User/natural', [
            'json' => $data
        ]);
    }

    // Wallet Methods
    public function createWallet(array $data)
    {
        return $this->request('POST', '/v2.01/Wallets', [
            'json' => $data
        ]);
    }

    // Payment Methods
    public function createPayIn(array $data)
    {
        return $this->request('POST', '/v2.01/PayIns/card/direct', [
            'json' => $data
        ]);
    }

    public function createPayOut(array $data)
    {
        return $this->request('POST', '/v2.01/PayOuts', [
            'json' => $data
        ]);
    }

    public function createTransfer(array $data)
    {
        return $this->request('POST', '/v2.01/Transfers', [
            'json' => $data
        ]);
    }
} 