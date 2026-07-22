<?php

declare(strict_types=1);

namespace ChuckBartowski\OvhSdk\Api;

use ChuckBartowski\OvhSdk\Response\ApiResponse;

final class DomainApi extends AbstractApi
{
    public function list(array $query = []): ApiResponse
    {
        return $this->get('/domain', $query);
    }

    public function find(string $domain): ApiResponse
    {
        return $this->get('/domain/'.$this->encode($domain));
    }

    public function serviceInfos(string $domain): ApiResponse
    {
        return $this->get(sprintf('/domain/%s/serviceInfos', $this->encode($domain)));
    }

    public function nameservers(string $domain): ApiResponse
    {
        return $this->get(sprintf('/domain/%s/nameServer', $this->encode($domain)));
    }

    public function updateNameservers(string $domain, array $nameServers): ApiResponse
    {
        return $this->post(sprintf('/domain/%s/nameServers/update', $this->encode($domain)), ['nameServers' => $nameServers]);
    }

    public function zones(): ApiResponse
    {
        return $this->get('/domain/zone');
    }

    public function zone(string $zone): ApiResponse
    {
        return $this->get('/domain/zone/'.$this->encode($zone));
    }

    public function records(string $zone, array $filters = []): ApiResponse
    {
        return $this->get(sprintf('/domain/zone/%s/record', $this->encode($zone)), $filters);
    }

    public function record(string $zone, int $recordId): ApiResponse
    {
        return $this->get(sprintf('/domain/zone/%s/record/%d', $this->encode($zone), $recordId));
    }

    public function addRecord(string $zone, string $fieldType, string $target, string $subDomain = '', ?int $ttl = null): ApiResponse
    {
        return $this->post(sprintf('/domain/zone/%s/record', $this->encode($zone)), array_filter([
            'fieldType' => $fieldType,
            'subDomain' => $subDomain,
            'target' => $target,
            'ttl' => $ttl,
        ], static fn (mixed $v): bool => null !== $v));
    }

    public function updateRecord(string $zone, int $recordId, array $fields): ApiResponse
    {
        return $this->put(sprintf('/domain/zone/%s/record/%d', $this->encode($zone), $recordId), $fields);
    }

    public function deleteRecord(string $zone, int $recordId): ApiResponse
    {
        return $this->delete(sprintf('/domain/zone/%s/record/%d', $this->encode($zone), $recordId));
    }

    public function refreshZone(string $zone): ApiResponse
    {
        return $this->post(sprintf('/domain/zone/%s/refresh', $this->encode($zone)));
    }

    public function exportZone(string $zone): ApiResponse
    {
        return $this->get(sprintf('/domain/zone/%s/export', $this->encode($zone)));
    }

    public function dnssec(string $domain): ApiResponse
    {
        return $this->get(sprintf('/domain/%s/dnssec', $this->encode($domain)));
    }
}
