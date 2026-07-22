<?php

declare(strict_types=1);

namespace ChuckBartowski\OvhSdk\Api;

use ChuckBartowski\OvhSdk\Response\ApiResponse;

final class VrackApi extends AbstractApi
{
    public function list(): ApiResponse
    {
        return $this->get('/vrack');
    }

    public function find(string $vrack): ApiResponse
    {
        return $this->get('/vrack/'.$this->encode($vrack));
    }

    public function dedicatedServers(string $vrack): ApiResponse
    {
        return $this->get(sprintf('/vrack/%s/dedicatedServer', $this->encode($vrack)));
    }

    public function addDedicatedServer(string $vrack, string $server): ApiResponse
    {
        return $this->post(sprintf('/vrack/%s/dedicatedServer', $this->encode($vrack)), ['dedicatedServer' => $server]);
    }

    public function removeDedicatedServer(string $vrack, string $server): ApiResponse
    {
        return $this->delete(sprintf('/vrack/%s/dedicatedServer/%s', $this->encode($vrack), $this->encode($server)));
    }

    public function cloudProjects(string $vrack): ApiResponse
    {
        return $this->get(sprintf('/vrack/%s/cloudProject', $this->encode($vrack)));
    }

    public function addCloudProject(string $vrack, string $project): ApiResponse
    {
        return $this->post(sprintf('/vrack/%s/cloudProject', $this->encode($vrack)), ['project' => $project]);
    }

    public function ips(string $vrack): ApiResponse
    {
        return $this->get(sprintf('/vrack/%s/ip', $this->encode($vrack)));
    }

    public function addIp(string $vrack, string $block): ApiResponse
    {
        return $this->post(sprintf('/vrack/%s/ip', $this->encode($vrack)), ['block' => $block]);
    }
}
