<?php

declare(strict_types=1);

namespace ChuckBartowski\OvhSdk\Api;

use ChuckBartowski\OvhSdk\Response\ApiResponse;

final class LoadBalancerApi extends AbstractApi
{
    public function list(): ApiResponse
    {
        return $this->get('/ipLoadbalancing');
    }

    public function find(string $service): ApiResponse
    {
        return $this->get('/ipLoadbalancing/'.$this->encode($service));
    }

    public function frontends(string $service, string $type = 'http'): ApiResponse
    {
        return $this->get(sprintf('/ipLoadbalancing/%s/%s/frontend', $this->encode($service), $this->encode($type)));
    }

    public function createFrontend(string $service, string $type, array $options): ApiResponse
    {
        return $this->post(sprintf('/ipLoadbalancing/%s/%s/frontend', $this->encode($service), $this->encode($type)), $options);
    }

    public function backends(string $service, string $type = 'http'): ApiResponse
    {
        return $this->get(sprintf('/ipLoadbalancing/%s/%s/farm', $this->encode($service), $this->encode($type)));
    }

    public function createBackend(string $service, string $type, array $options): ApiResponse
    {
        return $this->post(sprintf('/ipLoadbalancing/%s/%s/farm', $this->encode($service), $this->encode($type)), $options);
    }

    public function servers(string $service, string $type, int $farmId): ApiResponse
    {
        return $this->get(sprintf('/ipLoadbalancing/%s/%s/farm/%d/server', $this->encode($service), $this->encode($type), $farmId));
    }

    public function addServer(string $service, string $type, int $farmId, array $options): ApiResponse
    {
        return $this->post(sprintf('/ipLoadbalancing/%s/%s/farm/%d/server', $this->encode($service), $this->encode($type), $farmId), $options);
    }

    public function refresh(string $service): ApiResponse
    {
        return $this->post(sprintf('/ipLoadbalancing/%s/refresh', $this->encode($service)));
    }

    public function pendingChanges(string $service): ApiResponse
    {
        return $this->get(sprintf('/ipLoadbalancing/%s/pendingChanges', $this->encode($service)));
    }
}
