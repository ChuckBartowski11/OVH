<?php

declare(strict_types=1);

namespace ChuckBartowski\OvhSdk\Api;

use ChuckBartowski\OvhSdk\Response\ApiResponse;

final class SupportApi extends AbstractApi
{
    public function tickets(array $query = []): ApiResponse
    {
        return $this->get('/support/tickets', $query);
    }

    public function ticket(int $ticketId): ApiResponse
    {
        return $this->get('/support/tickets/'.$ticketId);
    }

    public function create(array $options): ApiResponse
    {
        return $this->post('/support/tickets/create', $options);
    }

    public function reply(int $ticketId, string $body): ApiResponse
    {
        return $this->post(sprintf('/support/tickets/%d/reply', $ticketId), ['body' => $body]);
    }

    public function close(int $ticketId): ApiResponse
    {
        return $this->post(sprintf('/support/tickets/%d/close', $ticketId));
    }

    public function messages(int $ticketId): ApiResponse
    {
        return $this->get(sprintf('/support/tickets/%d/messages', $ticketId));
    }
}
