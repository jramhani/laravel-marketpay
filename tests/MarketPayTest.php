<?php

namespace Jramhani\LaravelMarketPay\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Jramhani\LaravelMarketPay\MarketPay;

class MarketPayTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_can_create_a_natural_user()
    {
        // Mock response
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'access_token' => 'test-token',
                'token_type' => 'bearer',
                'expires_in' => 3600,
            ])),
            new Response(200, [], json_encode([
                'Id' => 'user_123',
                'FirstName' => 'John',
                'LastName' => 'Doe',
                'Email' => 'john@example.com',
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $marketPay = new MarketPay(config('marketpay'));
        $marketPay->setClient($client);

        $response = $marketPay->createUser([
            'FirstName' => 'John',
            'LastName' => 'Doe',
            'Email' => 'john@example.com',
            'Birthday' => '1990-01-01',
            'Nationality' => 'FR',
            'CountryOfResidence' => 'FR',
        ]);

        $this->assertEquals('user_123', $response['Id']);
        $this->assertEquals('John', $response['FirstName']);
        $this->assertEquals('Doe', $response['LastName']);
        $this->assertEquals('john@example.com', $response['Email']);
    }

    /** @test */
    public function it_can_create_a_wallet()
    {
        // Mock response
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'access_token' => 'test-token',
                'token_type' => 'bearer',
                'expires_in' => 3600,
            ])),
            new Response(200, [], json_encode([
                'Id' => 'wallet_123',
                'Description' => 'Main wallet',
                'Currency' => 'EUR',
                'Balance' => [
                    'Currency' => 'EUR',
                    'Amount' => 0,
                ],
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $marketPay = new MarketPay(config('marketpay'));
        $marketPay->setClient($client);

        $response = $marketPay->createWallet([
            'Owners' => ['user_123'],
            'Description' => 'Main wallet',
            'Currency' => 'EUR',
        ]);

        $this->assertEquals('wallet_123', $response['Id']);
        $this->assertEquals('Main wallet', $response['Description']);
        $this->assertEquals('EUR', $response['Currency']);
    }

    /** @test */
    public function it_can_create_a_card_registration()
    {
        // Mock response
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'access_token' => 'test-token',
                'token_type' => 'bearer',
                'expires_in' => 3600,
            ])),
            new Response(200, [], json_encode([
                'Id' => 'card_reg_123',
                'UserId' => 'user_123',
                'Currency' => 'EUR',
                'Status' => 'CREATED',
                'CardRegistrationURL' => 'https://api.sandbox.market-pay.com/card/register',
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $marketPay = new MarketPay(config('marketpay'));
        $marketPay->setClient($client);

        $response = $marketPay->createCardRegistration([
            'UserId' => 'user_123',
            'Currency' => 'EUR',
        ]);

        $this->assertEquals('card_reg_123', $response['Id']);
        $this->assertEquals('user_123', $response['UserId']);
        $this->assertEquals('EUR', $response['Currency']);
        $this->assertEquals('CREATED', $response['Status']);
    }

    /** @test */
    public function it_can_create_a_pay_in()
    {
        // Mock response
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'access_token' => 'test-token',
                'token_type' => 'bearer',
                'expires_in' => 3600,
            ])),
            new Response(200, [], json_encode([
                'Id' => 'payin_123',
                'AuthorId' => 'user_123',
                'CreditedWalletId' => 'wallet_123',
                'Status' => 'SUCCEEDED',
                'DebitedFunds' => [
                    'Currency' => 'EUR',
                    'Amount' => 1000,
                ],
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $marketPay = new MarketPay(config('marketpay'));
        $marketPay->setClient($client);

        $response = $marketPay->createPayIn([
            'AuthorId' => 'user_123',
            'CreditedWalletId' => 'wallet_123',
            'DebitedFunds' => [
                'Currency' => 'EUR',
                'Amount' => 1000,
            ],
            'Fees' => [
                'Currency' => 'EUR',
                'Amount' => 0,
            ],
            'CardId' => 'card_123',
        ]);

        $this->assertEquals('payin_123', $response['Id']);
        $this->assertEquals('user_123', $response['AuthorId']);
        $this->assertEquals('wallet_123', $response['CreditedWalletId']);
        $this->assertEquals('SUCCEEDED', $response['Status']);
    }
} 