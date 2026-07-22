<?php

declare(strict_types=1);

namespace ChuckBartowski\OvhSdk\Api;

use ChuckBartowski\OvhSdk\Response\ApiResponse;

final class IpApi extends AbstractApi
{
    public function list(array $query = []): ApiResponse
    {
        return $this->get('/ip', $query);
    }

    public function find(string $ip): ApiResponse
    {
        return $this->get('/ip/'.$this->encode($ip));
    }

    public function update(string $ip, array $fields): ApiResponse
    {
        return $this->put('/ip/'.$this->encode($ip), $fields);
    }

    public function reverse(string $ip): ApiResponse
    {
        return $this->get(sprintf('/ip/%s/reverse', $this->encode($ip)));
    }

    public function setReverse(string $ip, string $ipReverse, string $reverse): ApiResponse
    {
        return $this->post(sprintf('/ip/%s/reverse', $this->encode($ip)), [
            'ipReverse' => $ipReverse,
            'reverse' => $reverse,
        ]);
    }

    public function deleteReverse(string $ip, string $ipReverse): ApiResponse
    {
        return $this->delete(sprintf('/ip/%s/reverse/%s', $this->encode($ip), $this->encode($ipReverse)));
    }

    public function move(string $ip, string $to): ApiResponse
    {
        return $this->post(sprintf('/ip/%s/move', $this->encode($ip)), ['to' => $to]);
    }

    public function park(string $ip): ApiResponse
    {
        return $this->post(sprintf('/ip/%s/park', $this->encode($ip)));
    }

    public function firewall(string $ip, string $ipOnFirewall): ApiResponse
    {
        return $this->get(sprintf('/ip/%s/firewall/%s', $this->encode($ip), $this->encode($ipOnFirewall)));
    }

    public function enableFirewall(string $ip, string $ipOnFirewall): ApiResponse
    {
        return $this->post(sprintf('/ip/%s/firewall', $this->encode($ip)), ['ipOnFirewall' => $ipOnFirewall]);
    }

    public function firewallRules(string $ip, string $ipOnFirewall): ApiResponse
    {
        return $this->get(sprintf('/ip/%s/firewall/%s/rule', $this->encode($ip), $this->encode($ipOnFirewall)));
    }

    public function addFirewallRule(string $ip, string $ipOnFirewall, array $rule): ApiResponse
    {
        return $this->post(sprintf('/ip/%s/firewall/%s/rule', $this->encode($ip), $this->encode($ipOnFirewall)), $rule);
    }

    public function mitigation(string $ip): ApiResponse
    {
        return $this->get(sprintf('/ip/%s/mitigation', $this->encode($ip)));
    }

    public function enableMitigation(string $ip, string $ipOnMitigation): ApiResponse
    {
        return $this->post(sprintf('/ip/%s/mitigation', $this->encode($ip)), ['ipOnMitigation' => $ipOnMitigation]);
    }
}
