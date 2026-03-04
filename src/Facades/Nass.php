<?php

namespace Nass\Facades;

use Illuminate\Support\Facades\Facade;
use Nass\Services\AuthService;
use Nass\Services\TransactionService;

/**
 * @method static AuthService auth()
 * @method static TransactionService transactions()
 * @method static array get(string $endpoint, array $query = [])
 * @method static array post(string $endpoint, array $data = [])
 * @method static \Nass\Nass setToken(string $token)
 * @method static string|null getToken()
 *
 * @see \Nass\Nass
 */
class Nass extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Nass\Nass::class;
    }
}
