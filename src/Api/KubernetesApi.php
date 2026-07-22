<?php

declare(strict_types=1);

namespace ChuckBartowski\OvhSdk\Api;

use ChuckBartowski\OvhSdk\Response\ApiResponse;

final class KubernetesApi extends AbstractApi
{
    public function clusters(string $projectId): ApiResponse
    {
        return $this->get($this->base($projectId));
    }

    public function cluster(string $projectId, string $clusterId): ApiResponse
    {
        return $this->get($this->base($projectId).'/'.$this->encode($clusterId));
    }

    public function create(string $projectId, array $options): ApiResponse
    {
        return $this->post($this->base($projectId), $options);
    }

    public function update(string $projectId, string $clusterId, array $fields): ApiResponse
    {
        return $this->put($this->base($projectId).'/'.$this->encode($clusterId), $fields);
    }

    public function deleteCluster(string $projectId, string $clusterId): ApiResponse
    {
        return $this->delete($this->base($projectId).'/'.$this->encode($clusterId));
    }

    public function kubeconfig(string $projectId, string $clusterId): ApiResponse
    {
        return $this->post(sprintf('%s/%s/kubeconfig', $this->base($projectId), $this->encode($clusterId)));
    }

    public function reset(string $projectId, string $clusterId, array $options = []): ApiResponse
    {
        return $this->post(sprintf('%s/%s/reset', $this->base($projectId), $this->encode($clusterId)), $options);
    }

    public function nodePools(string $projectId, string $clusterId): ApiResponse
    {
        return $this->get(sprintf('%s/%s/nodepool', $this->base($projectId), $this->encode($clusterId)));
    }

    public function createNodePool(string $projectId, string $clusterId, array $options): ApiResponse
    {
        return $this->post(sprintf('%s/%s/nodepool', $this->base($projectId), $this->encode($clusterId)), $options);
    }

    public function updateNodePool(string $projectId, string $clusterId, string $poolId, array $fields): ApiResponse
    {
        return $this->put(sprintf('%s/%s/nodepool/%s', $this->base($projectId), $this->encode($clusterId), $this->encode($poolId)), $fields);
    }

    public function deleteNodePool(string $projectId, string $clusterId, string $poolId): ApiResponse
    {
        return $this->delete(sprintf('%s/%s/nodepool/%s', $this->base($projectId), $this->encode($clusterId), $this->encode($poolId)));
    }

    public function nodes(string $projectId, string $clusterId): ApiResponse
    {
        return $this->get(sprintf('%s/%s/node', $this->base($projectId), $this->encode($clusterId)));
    }

    private function base(string $projectId): string
    {
        return sprintf('/cloud/project/%s/kube', $this->encode($projectId));
    }
}
