<?php

namespace Nass\Services;

use Nass\Nass;

class TransactionService
{
    protected Nass $client;

    public function __construct(Nass $client)
    {
        $this->client = $client;
    }

    /**
     * Create a new payment transaction.
     *
     * @param  array{
     *     orderId: string,
     *     orderDesc: string,
     *     amount: float,
     *     currency: string,
     *     transactionType: string,
     *     backRef: string,
     *     notifyUrl: string
     * } $data
     * @return array{
     *     success: bool,
     *     code: int,
     *     status_code: int,
     *     data: array{
     *         url: string,
     *         pSign: string,
     *         transactionParams: array
     *     }
     * }
     */
    public function create(array $data): array
    {
        return $this->client->post('transaction', $data);
    }

    /**
     * Check the status of an existing transaction.
     *
     * Note: Status checks are only available within 24 hours of transaction initiation.
     * For long-term reference, use the RRN field instead of orderId.
     *
     * @param  string  $orderId  The order ID of the transaction to check
     * @return array{
     *     success: bool,
     *     code: int,
     *     status_code: int,
     *     data: array{
     *         terminal: string,
     *         actionCode: string,
     *         responseCode: string,
     *         statusMsg: string,
     *         card: string,
     *         amount: string,
     *         currency: string,
     *         tranDate: string,
     *         rrn: string,
     *         intRef: string,
     *         orderId: string
     *     }
     * }
     */
    public function checkStatus(string $orderId): array
    {
        return $this->client->get("transaction/{$orderId}/checkStatus");
    }
}
