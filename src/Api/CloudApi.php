<?php

declare(strict_types=1);

namespace ChuckBartowski\OvhSdk\Api;

use ChuckBartowski\OvhSdk\Response\ApiResponse;

final class CloudApi extends AbstractApi
{
    public function projects(): ApiResponse
    {
        return $this->get('/cloud/project');
    }

    public function project(string $projectId): ApiResponse
    {
        return $this->get('/cloud/project/'.$this->encode($projectId));
    }

    public function instances(string $projectId, array $query = []): ApiResponse
    {
        return $this->get($this->path($projectId, '/instance'), $query);
    }

    public function instance(string $projectId, string $instanceId): ApiResponse
    {
        return $this->get($this->path($projectId, '/instance/'.$this->encode($instanceId)));
    }

    public function createInstance(string $projectId, array $options): ApiResponse
    {
        return $this->post($this->path($projectId, '/instance'), $options);
    }

    public function deleteInstance(string $projectId, string $instanceId): ApiResponse
    {
        return $this->delete($this->path($projectId, '/instance/'.$this->encode($instanceId)));
    }

    public function rebootInstance(string $projectId, string $instanceId, string $type = 'soft'): ApiResponse
    {
        return $this->post($this->path($projectId, sprintf('/instance/%s/reboot', $this->encode($instanceId))), ['type' => $type]);
    }

    public function startInstance(string $projectId, string $instanceId): ApiResponse
    {
        return $this->post($this->path($projectId, sprintf('/instance/%s/start', $this->encode($instanceId))));
    }

    public function stopInstance(string $projectId, string $instanceId): ApiResponse
    {
        return $this->post($this->path($projectId, sprintf('/instance/%s/stop', $this->encode($instanceId))));
    }

    public function reinstallInstance(string $projectId, string $instanceId, string $imageId): ApiResponse
    {
        return $this->post($this->path($projectId, sprintf('/instance/%s/reinstall', $this->encode($instanceId))), ['imageId' => $imageId]);
    }

    public function resizeInstance(string $projectId, string $instanceId, string $flavorId): ApiResponse
    {
        return $this->post($this->path($projectId, sprintf('/instance/%s/resize', $this->encode($instanceId))), ['flavorId' => $flavorId]);
    }

    public function volumes(string $projectId, array $query = []): ApiResponse
    {
        return $this->get($this->path($projectId, '/volume'), $query);
    }

    public function createVolume(string $projectId, array $options): ApiResponse
    {
        return $this->post($this->path($projectId, '/volume'), $options);
    }

    public function deleteVolume(string $projectId, string $volumeId): ApiResponse
    {
        return $this->delete($this->path($projectId, '/volume/'.$this->encode($volumeId)));
    }

    public function attachVolume(string $projectId, string $volumeId, string $instanceId): ApiResponse
    {
        return $this->post($this->path($projectId, sprintf('/volume/%s/attach', $this->encode($volumeId))), ['instanceId' => $instanceId]);
    }

    public function detachVolume(string $projectId, string $volumeId, string $instanceId): ApiResponse
    {
        return $this->post($this->path($projectId, sprintf('/volume/%s/detach', $this->encode($volumeId))), ['instanceId' => $instanceId]);
    }

    public function snapshots(string $projectId): ApiResponse
    {
        return $this->get($this->path($projectId, '/snapshot'));
    }

    public function volumeSnapshots(string $projectId): ApiResponse
    {
        return $this->get($this->path($projectId, '/volume/snapshot'));
    }

    public function images(string $projectId, array $query = []): ApiResponse
    {
        return $this->get($this->path($projectId, '/image'), $query);
    }

    public function flavors(string $projectId, array $query = []): ApiResponse
    {
        return $this->get($this->path($projectId, '/flavor'), $query);
    }

    public function regions(string $projectId): ApiResponse
    {
        return $this->get($this->path($projectId, '/region'));
    }

    public function sshKeys(string $projectId): ApiResponse
    {
        return $this->get($this->path($projectId, '/sshkey'));
    }

    public function addSshKey(string $projectId, string $name, string $publicKey, ?string $region = null): ApiResponse
    {
        return $this->post($this->path($projectId, '/sshkey'), array_filter([
            'name' => $name,
            'publicKey' => $publicKey,
            'region' => $region,
        ]));
    }

    public function privateNetworks(string $projectId): ApiResponse
    {
        return $this->get($this->path($projectId, '/network/private'));
    }

    public function createPrivateNetwork(string $projectId, array $options): ApiResponse
    {
        return $this->post($this->path($projectId, '/network/private'), $options);
    }

    public function publicNetworks(string $projectId): ApiResponse
    {
        return $this->get($this->path($projectId, '/network/public'));
    }

    public function users(string $projectId): ApiResponse
    {
        return $this->get($this->path($projectId, '/user'));
    }

    public function createUser(string $projectId, array $options): ApiResponse
    {
        return $this->post($this->path($projectId, '/user'), $options);
    }

    public function storageContainers(string $projectId): ApiResponse
    {
        return $this->get($this->path($projectId, '/storage'));
    }

    public function usageCurrent(string $projectId): ApiResponse
    {
        return $this->get($this->path($projectId, '/usage/current'));
    }

    public function fetch(string $projectId, string $path, array $query = []): ApiResponse
    {
        return $this->get($this->path($projectId, $path), $query);
    }

    private function path(string $projectId, string $suffix): string
    {
        return sprintf('/cloud/project/%s%s', $this->encode($projectId), $suffix);
    }
}
