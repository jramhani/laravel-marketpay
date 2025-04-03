<?php

namespace Jramhani\LaravelMarketPay\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static bool ping()
 * @method static array createUser(array $data)
 * @method static array getUser(string $userId)
 * @method static array updateUser(string $userId, array $data)
 * @method static array listUsers(array $filters = [])
 * @method static array createBankAccount(string $userId, array $data)
 * @method static array getBankAccount(string $userId, string $bankAccountId)
 * @method static array listBankAccounts(string $userId)
 * @method static array deactivateBankAccount(string $userId, string $bankAccountId)
 * @method static array getCard(string $cardId)
 * @method static array listCards(string $userId)
 * @method static array deactivateCard(string $cardId)
 * @method static array createCardRegistration(array $data)
 * @method static array getWallet(string $walletId)
 * @method static array createWallet(array $data)
 * @method static array listWalletTransactions(string $walletId, array $filters = [])
 * @method static array createPayIn(array $data)
 * @method static array getPayIn(string $payInId)
 * @method static array createPayOut(array $data)
 * @method static array getPayOut(string $payOutId)
 * @method static array createTransfer(array $data)
 * @method static array getTransfer(string $transferId)
 * @method static array createPayInRefund(string $payInId, array $data)
 * @method static array createTransferRefund(string $transferId, array $data)
 * @method static array getRefund(string $refundId)
 *
 * @see \Jramhani\LaravelMarketPay\MarketPay
 */
class MarketPay extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'marketpay';
    }
} 