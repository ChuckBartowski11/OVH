<?php

declare(strict_types=1);

namespace ChuckBartowski\OvhSdk\Api;

use ChuckBartowski\OvhSdk\Response\ApiResponse;

final class EmailApi extends AbstractApi
{
    public function domains(): ApiResponse
    {
        return $this->get('/email/domain');
    }

    public function domain(string $domain): ApiResponse
    {
        return $this->get('/email/domain/'.$this->encode($domain));
    }

    public function accounts(string $domain, array $query = []): ApiResponse
    {
        return $this->get(sprintf('/email/domain/%s/account', $this->encode($domain)), $query);
    }

    public function account(string $domain, string $account): ApiResponse
    {
        return $this->get(sprintf('/email/domain/%s/account/%s', $this->encode($domain), $this->encode($account)));
    }

    public function createAccount(string $domain, string $accountName, string $password, array $options = []): ApiResponse
    {
        return $this->post(sprintf('/email/domain/%s/account', $this->encode($domain)), array_merge($options, [
            'accountName' => $accountName,
            'password' => $password,
        ]));
    }

    public function deleteAccount(string $domain, string $account): ApiResponse
    {
        return $this->delete(sprintf('/email/domain/%s/account/%s', $this->encode($domain), $this->encode($account)));
    }

    public function changePassword(string $domain, string $account, string $password): ApiResponse
    {
        return $this->post(sprintf('/email/domain/%s/account/%s/changePassword', $this->encode($domain), $this->encode($account)), ['password' => $password]);
    }

    public function redirections(string $domain, array $query = []): ApiResponse
    {
        return $this->get(sprintf('/email/domain/%s/redirection', $this->encode($domain)), $query);
    }

    public function createRedirection(string $domain, string $from, string $to, bool $localCopy = false): ApiResponse
    {
        return $this->post(sprintf('/email/domain/%s/redirection', $this->encode($domain)), [
            'from' => $from,
            'to' => $to,
            'localCopy' => $localCopy,
        ]);
    }

    public function mailingLists(string $domain): ApiResponse
    {
        return $this->get(sprintf('/email/domain/%s/mailingList', $this->encode($domain)));
    }

    public function exchangeServices(): ApiResponse
    {
        return $this->get('/email/exchange');
    }

    public function exchangeAccounts(string $organization, string $service): ApiResponse
    {
        return $this->get(sprintf('/email/exchange/%s/service/%s/account', $this->encode($organization), $this->encode($service)));
    }
}
