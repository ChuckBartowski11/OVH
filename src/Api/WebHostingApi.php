<?php

declare(strict_types=1);

namespace ChuckBartowski\OvhSdk\Api;

use ChuckBartowski\OvhSdk\Response\ApiResponse;

final class WebHostingApi extends AbstractApi
{
    public function list(): ApiResponse
    {
        return $this->get('/hosting/web');
    }

    public function find(string $service): ApiResponse
    {
        return $this->get('/hosting/web/'.$this->encode($service));
    }

    public function attachedDomains(string $service): ApiResponse
    {
        return $this->get(sprintf('/hosting/web/%s/attachedDomain', $this->encode($service)));
    }

    public function attachDomain(string $service, array $options): ApiResponse
    {
        return $this->post(sprintf('/hosting/web/%s/attachedDomain', $this->encode($service)), $options);
    }

    public function databases(string $service): ApiResponse
    {
        return $this->get(sprintf('/hosting/web/%s/database', $this->encode($service)));
    }

    public function createDatabase(string $service, array $options): ApiResponse
    {
        return $this->post(sprintf('/hosting/web/%s/database/create', $this->encode($service)), $options);
    }

    public function cron(string $service): ApiResponse
    {
        return $this->get(sprintf('/hosting/web/%s/cron', $this->encode($service)));
    }

    public function users(string $service): ApiResponse
    {
        return $this->get(sprintf('/hosting/web/%s/user', $this->encode($service)));
    }

    public function ssl(string $service): ApiResponse
    {
        return $this->get(sprintf('/hosting/web/%s/ssl', $this->encode($service)));
    }

    public function requestSsl(string $service, array $options = []): ApiResponse
    {
        return $this->post(sprintf('/hosting/web/%s/ssl', $this->encode($service)), $options);
    }

    public function ovhConfigs(string $service): ApiResponse
    {
        return $this->get(sprintf('/hosting/web/%s/ovhConfig', $this->encode($service)));
    }

    public function tasks(string $service, array $query = []): ApiResponse
    {
        return $this->get(sprintf('/hosting/web/%s/tasks', $this->encode($service)), $query);
    }
}
