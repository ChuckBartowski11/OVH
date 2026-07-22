<?php

declare(strict_types=1);

namespace ChuckBartowski\OvhSdk\Api;

use ChuckBartowski\OvhSdk\Response\ApiResponse;

final class VoipApi extends AbstractApi
{
    public function billingAccounts(): ApiResponse
    {
        return $this->get('/telephony');
    }

    public function billingAccount(string $account): ApiResponse
    {
        return $this->get('/telephony/'.$this->encode($account));
    }

    public function lines(string $account): ApiResponse
    {
        return $this->get(sprintf('/telephony/%s/line', $this->encode($account)));
    }

    public function line(string $account, string $service): ApiResponse
    {
        return $this->get(sprintf('/telephony/%s/line/%s', $this->encode($account), $this->encode($service)));
    }

    public function numbers(string $account): ApiResponse
    {
        return $this->get(sprintf('/telephony/%s/number', $this->encode($account)));
    }

    public function calls(string $account, string $service, array $query = []): ApiResponse
    {
        return $this->get(sprintf('/telephony/%s/service/%s/voiceConsumption', $this->encode($account), $this->encode($service)), $query);
    }

    public function click2Call(string $account, string $service, string $calledNumber, string $callingNumber): ApiResponse
    {
        return $this->post(sprintf('/telephony/%s/line/%s/click2Call', $this->encode($account), $this->encode($service)), [
            'calledNumber' => $calledNumber,
            'callingNumber' => $callingNumber,
        ]);
    }
}
