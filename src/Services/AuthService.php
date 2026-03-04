<?php

namespace Nass\Services;

use Nass\Nass;

class AuthService
{
    protected Nass $client;

    public function __construct(Nass $client)
    {
        $this->client = $client;
    }

    /**
     * Authenticate the merchant and retrieve a Bearer access token.
     *
     * @param  string|null  $username  Merchant username (defaults to config value)
     * @param  string|null  $password  Merchant password (defaults to config value)
     * @return array{access_token: string}
     */
    public function login(?string $username = null, ?string $password = null): array
    {
        return $this->client->post('auth/merchant/login', [
            'username' => $username ?? config('nass.username', ''),
            'password' => $password ?? config('nass.password', ''),
        ]);
    }
}
