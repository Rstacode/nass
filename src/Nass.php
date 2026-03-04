<?php

namespace Nass;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Nass\Exceptions\NassException;
use Nass\Services\AuthService;
use Nass\Services\TransactionService;

class Nass
{
    protected Client $client;
    protected string $baseUrl;
    protected ?string $token;

    public function __construct(?string $baseUrl = null, ?string $token = null)
    {
        $this->baseUrl = $baseUrl ?? config('nass.base_url', 'https://gateway.nass.iq:9746');
        $this->token = $token;

        $this->buildClient();
    }

    /**
     * Set the Bearer token and rebuild the HTTP client.
     */
    public function setToken(string $token): static
    {
        $this->token = $token;
        $this->buildClient();

        return $this;
    }

    /**
     * Get the current Bearer token.
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * Make a GET request to the API.
     *
     * @throws NassException
     */
    public function get(string $endpoint, array $query = []): array
    {
        return $this->request('GET', $endpoint, ['query' => $query]);
    }

    /**
     * Make a POST request to the API.
     *
     * @throws NassException
     */
    public function post(string $endpoint, array $data = []): array
    {
        return $this->request('POST', $endpoint, ['json' => $data]);
    }

    /**
     * Make an HTTP request to the API.
     *
     * @throws NassException
     */
    protected function request(string $method, string $endpoint, array $options = []): array
    {
        try {
            $response = $this->client->request($method, $endpoint, $options);
            $body = json_decode($response->getBody()->getContents(), true);

            return $body ?? [];
        } catch (GuzzleException $e) {
            $response = [];
            $statusCode = 500;

            if (method_exists($e, 'getResponse') && $e->getResponse()) {
                $statusCode = $e->getResponse()->getStatusCode();
                $body = $e->getResponse()->getBody()->getContents();
                $response = json_decode($body, true) ?? [];
            }

            throw NassException::fromResponse(
                array_merge($response, ['error' => $e->getMessage()]),
                $statusCode
            );
        }
    }

    /**
     * Get the Auth service.
     */
    public function auth(): AuthService
    {
        return new AuthService($this);
    }

    /**
     * Get the Transaction service.
     */
    public function transactions(): TransactionService
    {
        return new TransactionService($this);
    }

    /**
     * Build or rebuild the Guzzle HTTP client with current headers.
     */
    protected function buildClient(): void
    {
        $baseUrl = $this->baseUrl;

        if (!str_ends_with($baseUrl, '/')) {
            $baseUrl .= '/';
        }

        $headers = [
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        ];

        if ($this->token) {
            $headers['Authorization'] = 'Bearer ' . $this->token;
        }

        $this->client = new Client([
            'base_uri' => $baseUrl,
            'timeout'  => config('nass.timeout', 30),
            'headers'  => $headers,
        ]);
    }
}
