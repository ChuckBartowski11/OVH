<?php

declare(strict_types=1);

use ChuckBartowski\OvhSdk\Client\OvhClient;
use ChuckBartowski\OvhSdk\Exception\OvhSdkExceptionInterface;
use ChuckBartowski\OvhSdk\Ovh;

if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

function ovhsdk_getConfigArray(): array
{
    return [
        'FriendlyName' => ['Type' => 'System', 'Value' => 'OVHcloud (SDK)'],
        'ApplicationKey' => ['Type' => 'text', 'Size' => '30', 'Description' => 'OVHcloud application key'],
        'ApplicationSecret' => ['Type' => 'password', 'Size' => '30', 'Description' => 'OVHcloud application secret'],
        'ConsumerKey' => ['Type' => 'password', 'Size' => '30', 'Description' => 'OVHcloud consumer key'],
        'Endpoint' => ['Type' => 'dropdown', 'Options' => 'ovh-eu,ovh-ca,ovh-us', 'Default' => 'ovh-eu'],
    ];
}

function ovhsdk_ovh(array $params): Ovh
{
    return new Ovh(new OvhClient(
        applicationKey: $params['ApplicationKey'],
        applicationSecret: $params['ApplicationSecret'],
        consumerKey: $params['ConsumerKey'],
        endpoint: $params['Endpoint'] ?: 'ovh-eu',
    ));
}

function ovhsdk_domain(array $params): string
{
    return $params['domainname'] ?? ($params['sld'].'.'.$params['tld']);
}

function ovhsdk_GetNameservers(array $params): array
{
    try {
        $domain = ovhsdk_domain($params);
        $records = ovhsdk_ovh($params)->domains()->records($domain, ['fieldType' => 'NS', 'subDomain' => '']);
        $result = [];
        $i = 1;

        foreach ($records->items() as $recordId) {
            $record = ovhsdk_ovh($params)->domains()->record($domain, (int) $recordId);
            $result['ns'.$i] = rtrim((string) $record->data('target'), '.');
            ++$i;
        }

        return $result ?: ['error' => 'No nameservers found'];
    } catch (OvhSdkExceptionInterface $e) {
        return ['error' => $e->getMessage()];
    }
}

function ovhsdk_SaveNameservers(array $params): array
{
    try {
        $nameServers = array_values(array_filter([
            $params['ns1'] ?? '', $params['ns2'] ?? '', $params['ns3'] ?? '',
            $params['ns4'] ?? '', $params['ns5'] ?? '',
        ]));

        ovhsdk_ovh($params)->domains()->updateNameservers(ovhsdk_domain($params), array_map(
            static fn (string $host): array => ['host' => $host],
            $nameServers,
        ));

        return ['success' => true];
    } catch (OvhSdkExceptionInterface $e) {
        return ['error' => $e->getMessage()];
    }
}

function ovhsdk_GetDNS(array $params): array
{
    try {
        $ovh = ovhsdk_ovh($params);
        $zone = ovhsdk_domain($params);
        $records = [];

        foreach ($ovh->domains()->records($zone)->items() as $recordId) {
            $record = $ovh->domains()->record($zone, (int) $recordId)->data;
            $type = $record['fieldType'] ?? '';

            if (!in_array($type, ['A', 'AAAA', 'CNAME', 'MX', 'TXT', 'SRV'], true)) {
                continue;
            }

            $address = (string) ($record['target'] ?? '');
            $priority = '';

            if ('MX' === $type && preg_match('/^(\d+)\s+(.+)$/', $address, $m)) {
                $priority = $m[1];
                $address = $m[2];
            }

            $records[] = [
                'hostname' => ($record['subDomain'] ?? '') ?: '@',
                'type' => $type,
                'address' => $address,
                'priority' => $priority,
            ];
        }

        return $records;
    } catch (OvhSdkExceptionInterface $e) {
        return ['error' => $e->getMessage()];
    }
}

function ovhsdk_SaveDNS(array $params): array
{
    try {
        $ovh = ovhsdk_ovh($params);
        $zone = ovhsdk_domain($params);

        foreach ($ovh->domains()->records($zone)->items() as $recordId) {
            $ovh->domains()->deleteRecord($zone, (int) $recordId);
        }

        foreach ($params['dnsrecords'] as $record) {
            if ('' === trim((string) $record['address'])) {
                continue;
            }

            $target = $record['address'];

            if ('MX' === $record['type'] && '' !== (string) ($record['priority'] ?? '')) {
                $target = $record['priority'].' '.$record['address'];
            }

            $subDomain = '@' === $record['hostname'] ? '' : $record['hostname'];
            $ovh->domains()->addRecord($zone, $record['type'], $target, $subDomain);
        }

        $ovh->domains()->refreshZone($zone);

        return ['success' => true];
    } catch (OvhSdkExceptionInterface $e) {
        return ['error' => $e->getMessage()];
    }
}

function ovhsdk_TestConnection(array $params): array
{
    try {
        ovhsdk_ovh($params)->me()->info();

        return ['success' => true, 'error' => ''];
    } catch (OvhSdkExceptionInterface $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}
