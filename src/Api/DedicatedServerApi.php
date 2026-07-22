<?php

declare(strict_types=1);

namespace ChuckBartowski\OvhSdk\Api;

use ChuckBartowski\OvhSdk\Response\ApiResponse;

final class DedicatedServerApi extends AbstractApi
{
    public function list(): ApiResponse
    {
        return $this->get('/dedicated/server');
    }

    public function find(string $name): ApiResponse
    {
        return $this->get('/dedicated/server/'.$this->encode($name));
    }

    public function update(string $name, array $fields): ApiResponse
    {
        return $this->put('/dedicated/server/'.$this->encode($name), $fields);
    }

    public function serviceInfos(string $name): ApiResponse
    {
        return $this->get(sprintf('/dedicated/server/%s/serviceInfos', $this->encode($name)));
    }

    public function hardware(string $name): ApiResponse
    {
        return $this->get(sprintf('/dedicated/server/%s/specifications/hardware', $this->encode($name)));
    }

    public function network(string $name): ApiResponse
    {
        return $this->get(sprintf('/dedicated/server/%s/specifications/network', $this->encode($name)));
    }

    public function reboot(string $name): ApiResponse
    {
        return $this->post(sprintf('/dedicated/server/%s/reboot', $this->encode($name)));
    }

    public function reinstall(string $name, array $options): ApiResponse
    {
        return $this->post(sprintf('/dedicated/server/%s/install/start', $this->encode($name)), $options);
    }

    public function installStatus(string $name): ApiResponse
    {
        return $this->get(sprintf('/dedicated/server/%s/install/status', $this->encode($name)));
    }

    public function bootOptions(string $name): ApiResponse
    {
        return $this->get(sprintf('/dedicated/server/%s/boot', $this->encode($name)));
    }

    public function setMonitoring(string $name, bool $enabled): ApiResponse
    {
        return $this->put('/dedicated/server/'.$this->encode($name), ['monitoring' => $enabled]);
    }

    public function ipmiAccess(string $name, array $options): ApiResponse
    {
        return $this->post(sprintf('/dedicated/server/%s/features/ipmi/access', $this->encode($name)), $options);
    }

    public function interventions(string $name): ApiResponse
    {
        return $this->get(sprintf('/dedicated/server/%s/intervention', $this->encode($name)));
    }

    public function tasks(string $name, array $query = []): ApiResponse
    {
        return $this->get(sprintf('/dedicated/server/%s/task', $this->encode($name)), $query);
    }
}
