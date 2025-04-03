<?php

namespace Jramhani\LaravelMarketPay\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Jramhani\LaravelMarketPay\Exceptions\MarketPayException;
use Jramhani\LaravelMarketPay\MarketPay;
use Jramhani\LaravelMarketPay\DTOs\Money;
use PHPUnit\Framework\TestCase;
use Jramhani\LaravelMarketPay\Models\Money as OldMoney;

class MarketPayTest extends TestCase
{
    protected MarketPay $marketPay;
    protected MockHandler $mockHandler;
    protected array $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->config = [
            'client_id' => 'test_client_id',
            'client_secret' => 'test_client_secret',
            'environment' => 'sandbox',
            'base_url' => [
                'sandbox' => 'https://api.sandbox.marketpay.io',
                'production' => 'https://api.marketpay.io',
            ],
        ];

        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        $client = new Client(['handler' => $handlerStack]);

        $this->marketPay = new MarketPay($this->config);
        $this->marketPay->setClient($client);
    }

    /** @test */
    public function it_returns_true_when_ping_returns_200()
    {
        $this->mockHandler->append(
            new Response(200)
        );

        $result = $this->marketPay->ping();

        $this->assertTrue($result);
    }

    /** @test */
    public function it_throws_exception_when_ping_fails()
    {
        $this->mockHandler->append(
            new Response(500)
        );

        $this->expectException(MarketPayException::class);
        $this->expectExceptionMessage('API is not accessible');

        $this->marketPay->ping();
    }

    /** @test */
    public function it_can_create_a_natural_user()
    {
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

        $marketPay = new MarketPay($this->config);
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

        $marketPay = new MarketPay($this->config);
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

        $marketPay = new MarketPay($this->config);
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

        $marketPay = new MarketPay($this->config);
        $marketPay->setClient($client);

        $debitedFunds = new Money([
            'Currency' => 'EUR',
            'Amount' => 1000,
        ]);

        $fees = new Money([
            'Currency' => 'EUR',
            'Amount' => 0,
        ]);

        $response = $marketPay->createPayIn([
            'AuthorId' => 'user_123',
            'CreditedWalletId' => 'wallet_123',
            'DebitedFunds' => $debitedFunds,
            'Fees' => $fees,
            'CardId' => 'card_123',
        ]);

        $this->assertEquals('payin_123', $response['Id']);
        $this->assertEquals('user_123', $response['AuthorId']);
        $this->assertEquals('wallet_123', $response['CreditedWalletId']);
        $this->assertEquals('SUCCEEDED', $response['Status']);
    }
} 