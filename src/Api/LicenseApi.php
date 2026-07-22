<?php

declare(strict_types=1);

namespace ChuckBartowski\OvhSdk\Api;

use ChuckBartowski\OvhSdk\Response\ApiResponse;

final class LicenseApi extends AbstractApi
{
    public function windows(): ApiResponse
    {
        return $this->get('/license/windows');
    }

    public function cpanel(): ApiResponse
    {
        return $this->get('/license/cpanel');
    }

    public function plesk(): ApiResponse
    {
        return $this->get('/license/plesk');
    }

    public function directadmin(): ApiResponse
    {
        return $this->get('/license/directadmin');
    }

    public function office(): ApiResponse
    {
        return $this->get('/license/office');
    }

    public function find(string $type, string $service): ApiResponse
    {
        return $this->get(sprintf('/license/%s/%s', $this->encode($type), $this->encode($service)));
    }

    public function serviceInfos(string $type, string $service): ApiResponse
    {
        return $this->get(sprintf('/license/%s/%s/serviceInfos', $this->encode($type), $this->encode($service)));
    }
}
