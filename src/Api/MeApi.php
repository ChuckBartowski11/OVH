<?php

declare(strict_types=1);

namespace ChuckBartowski\OvhSdk\Api;

use ChuckBartowski\OvhSdk\Response\ApiResponse;

final class MeApi extends AbstractApi
{
    public function info(): ApiResponse
    {
        return $this->get('/me');
    }

    public function update(array $fields): ApiResponse
    {
        return $this->put('/me', $fields);
    }

    public function bills(array $query = []): ApiResponse
    {
        return $this->get('/me/bill', $query);
    }

    public function bill(string $id): ApiResponse
    {
        return $this->get('/me/bill/'.$this->encode($id));
    }

    public function orders(array $query = []): ApiResponse
    {
        return $this->get('/me/order', $query);
    }

    public function order(int $id): ApiResponse
    {
        return $this->get('/me/order/'.$id);
    }

    public function paymentMethods(array $query = []): ApiResponse
    {
        return $this->get('/me/payment/method', $query);
    }

    public function contacts(array $query = []): ApiResponse
    {
        return $this->get('/me/contact', $query);
    }

    public function sshKeys(): ApiResponse
    {
        return $this->get('/me/sshKey');
    }

    public function addSshKey(string $name, string $key): ApiResponse
    {
        return $this->post('/me/sshKey', ['keyName' => $name, 'key' => $key]);
    }

    public function deleteSshKey(string $name): ApiResponse
    {
        return $this->delete('/me/sshKey/'.$this->encode($name));
    }

    public function apiApplications(): ApiResponse
    {
        return $this->get('/me/api/application');
    }

    public function apiCredentials(array $query = []): ApiResponse
    {
        return $this->get('/me/api/credential', $query);
    }

    public function revokeCredential(int $credentialId): ApiResponse
    {
        return $this->delete('/me/api/credential/'.$credentialId);
    }

    public function iamPolicies(): ApiResponse
    {
        return $this->get('/me/identity/user');
    }

    public function fetch(string $path, array $query = []): ApiResponse
    {
        return $this->get('/me'.$path, $query);
    }
}
