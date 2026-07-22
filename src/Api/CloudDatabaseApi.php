<?php

declare(strict_types=1);

namespace ChuckBartowski\OvhSdk\Api;

use ChuckBartowski\OvhSdk\Response\ApiResponse;

final class CloudDatabaseApi extends AbstractApi
{
    public function services(string $projectId): ApiResponse
    {
        return $this->get($this->base($projectId).'/service');
    }

    public function list(string $projectId, string $engine): ApiResponse
    {
        return $this->get(sprintf('%s/%s', $this->base($projectId), $this->encode($engine)));
    }

    public function find(string $projectId, string $engine, string $clusterId): ApiResponse
    {
        return $this->get(sprintf('%s/%s/%s', $this->base($projectId), $this->encode($engine), $this->encode($clusterId)));
    }

    public function create(string $projectId, string $engine, array $options): ApiResponse
    {
        return $this->post(sprintf('%s/%s', $this->base($projectId), $this->encode($engine)), $options);
    }

    public function deleteCluster(string $projectId, string $engine, string $clusterId): ApiResponse
    {
        return $this->delete(sprintf('%s/%s/%s', $this->base($projectId), $this->encode($engine), $this->encode($clusterId)));
    }

    public function databases(string $projectId, string $engine, string $clusterId): ApiResponse
    {
        return $this->get(sprintf('%s/%s/%s/database', $this->base($projectId), $this->encode($engine), $this->encode($clusterId)));
    }

    public function users(string $projectId, string $engine, string $clusterId): ApiResponse
    {
        return $this->get(sprintf('%s/%s/%s/user', $this->base($projectId), $this->encode($engine), $this->encode($clusterId)));
    }

    public function createUser(string $projectId, string $engine, string $clusterId, array $options): ApiResponse
    {
        return $this->post(sprintf('%s/%s/%s/user', $this->base($projectId), $this->encode($engine), $this->encode($clusterId)), $options);
    }

    public function backups(string $projectId, string $engine, string $clusterId): ApiResponse
    {
        return $this->get(sprintf('%s/%s/%s/backup', $this->base($projectId), $this->encode($engine), $this->encode($clusterId)));
    }

    private function base(string $projectId): string
    {
        return sprintf('/cloud/project/%s/database', $this->encode($projectId));
    }
}
