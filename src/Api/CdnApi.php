<?php

declare(strict_types=1);

namespace ChuckBartowski\OvhSdk\Api;

use ChuckBartowski\OvhSdk\Response\ApiResponse;

final class CdnApi extends AbstractApi
{
    public function dedicatedServices(): ApiResponse
    {
        return $this->get('/cdn/dedicated');
    }

    public function dedicated(string $service): ApiResponse
    {
        return $this->get('/cdn/dedicated/'.$this->encode($service));
    }

    public function domains(string $service): ApiResponse
    {
        return $this->get(sprintf('/cdn/dedicated/%s/domains', $this->encode($service)));
    }

    public function addDomain(string $service, string $domain): ApiResponse
    {
        return $this->post(sprintf('/cdn/dedicated/%s/domains', $this->encode($service)), ['domain' => $domain]);
    }

    public function flushCache(string $service, string $domain): ApiResponse
    {
        return $this->post(sprintf('/cdn/dedicated/%s/domains/%s/flush', $this->encode($service), $this->encode($domain)));
    }

    public function websiteServices(): ApiResponse
    {
        return $this->get('/cdn/website');
    }

    public function website(string $service): ApiResponse
    {
        return $this->get('/cdn/website/'.$this->encode($service));
    }
}
