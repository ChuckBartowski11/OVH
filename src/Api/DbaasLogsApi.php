<?php

declare(strict_types=1);

namespace ChuckBartowski\OvhSdk\Api;

use ChuckBartowski\OvhSdk\Response\ApiResponse;

final class DbaasLogsApi extends AbstractApi
{
    public function services(): ApiResponse
    {
        return $this->get('/dbaas/logs');
    }

    public function find(string $serviceName): ApiResponse
    {
        return $this->get('/dbaas/logs/'.$this->encode($serviceName));
    }

    public function streams(string $serviceName): ApiResponse
    {
        return $this->get(sprintf('/dbaas/logs/%s/output/graylog/stream', $this->encode($serviceName)));
    }

    public function createStream(string $serviceName, array $options): ApiResponse
    {
        return $this->post(sprintf('/dbaas/logs/%s/output/graylog/stream', $this->encode($serviceName)), $options);
    }

    public function dashboards(string $serviceName): ApiResponse
    {
        return $this->get(sprintf('/dbaas/logs/%s/output/graylog/dashboard', $this->encode($serviceName)));
    }

    public function inputs(string $serviceName): ApiResponse
    {
        return $this->get(sprintf('/dbaas/logs/%s/input', $this->encode($serviceName)));
    }

    public function aliases(string $serviceName): ApiResponse
    {
        return $this->get(sprintf('/dbaas/logs/%s/output/graylog/alias', $this->encode($serviceName)));
    }
}
