<?php

declare(strict_types=1);

namespace ChuckBartowski\OvhSdk\Api;

use ChuckBartowski\OvhSdk\Response\ApiResponse;

final class SmsApi extends AbstractApi
{
    public function services(): ApiResponse
    {
        return $this->get('/sms');
    }

    public function find(string $service): ApiResponse
    {
        return $this->get('/sms/'.$this->encode($service));
    }

    public function send(string $service, array $receivers, string $message, array $options = []): ApiResponse
    {
        return $this->post(sprintf('/sms/%s/jobs', $this->encode($service)), array_merge($options, [
            'receivers' => $receivers,
            'message' => $message,
        ]));
    }

    public function jobs(string $service): ApiResponse
    {
        return $this->get(sprintf('/sms/%s/jobs', $this->encode($service)));
    }

    public function outgoing(string $service, array $query = []): ApiResponse
    {
        return $this->get(sprintf('/sms/%s/outgoing', $this->encode($service)), $query);
    }

    public function incoming(string $service, array $query = []): ApiResponse
    {
        return $this->get(sprintf('/sms/%s/incoming', $this->encode($service)), $query);
    }

    public function senders(string $service): ApiResponse
    {
        return $this->get(sprintf('/sms/%s/senders', $this->encode($service)));
    }
}
