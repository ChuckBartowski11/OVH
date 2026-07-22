<?php

declare(strict_types=1);

namespace ChuckBartowski\OvhSdk\Api;

use ChuckBartowski\OvhSdk\Response\ApiResponse;

final class VpsApi extends AbstractApi
{
    public function list(): ApiResponse
    {
        return $this->get('/vps');
    }

    public function find(string $name): ApiResponse
    {
        return $this->get('/vps/'.$this->encode($name));
    }

    public function update(string $name, array $fields): ApiResponse
    {
        return $this->put('/vps/'.$this->encode($name), $fields);
    }

    public function reboot(string $name): ApiResponse
    {
        return $this->post(sprintf('/vps/%s/reboot', $this->encode($name)));
    }

    public function start(string $name): ApiResponse
    {
        return $this->post(sprintf('/vps/%s/start', $this->encode($name)));
    }

    public function stop(string $name): ApiResponse
    {
        return $this->post(sprintf('/vps/%s/stop', $this->encode($name)));
    }

    public function reinstall(string $name, array $options): ApiResponse
    {
        return $this->post(sprintf('/vps/%s/reinstall', $this->encode($name)), $options);
    }

    public function images(string $name): ApiResponse
    {
        return $this->get(sprintf('/vps/%s/images/available', $this->encode($name)));
    }

    public function ips(string $name): ApiResponse
    {
        return $this->get(sprintf('/vps/%s/ips', $this->encode($name)));
    }

    public function snapshots(string $name): ApiResponse
    {
        return $this->get(sprintf('/vps/%s/snapshot', $this->encode($name)));
    }

    public function createSnapshot(string $name, ?string $description = null): ApiResponse
    {
        return $this->post(sprintf('/vps/%s/createSnapshot', $this->encode($name)), array_filter(['description' => $description]));
    }

    public function disks(string $name): ApiResponse
    {
        return $this->get(sprintf('/vps/%s/disks', $this->encode($name)));
    }

    public function tasks(string $name, array $query = []): ApiResponse
    {
        return $this->get(sprintf('/vps/%s/tasks', $this->encode($name)), $query);
    }
}
