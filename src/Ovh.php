<?php

declare(strict_types=1);

namespace ChuckBartowski\OvhSdk;

use ChuckBartowski\OvhSdk\Api\CdnApi;
use ChuckBartowski\OvhSdk\Api\CloudApi;
use ChuckBartowski\OvhSdk\Api\CloudDatabaseApi;
use ChuckBartowski\OvhSdk\Api\DbaasLogsApi;
use ChuckBartowski\OvhSdk\Api\DedicatedServerApi;
use ChuckBartowski\OvhSdk\Api\DomainApi;
use ChuckBartowski\OvhSdk\Api\EmailApi;
use ChuckBartowski\OvhSdk\Api\IpApi;
use ChuckBartowski\OvhSdk\Api\KubernetesApi;
use ChuckBartowski\OvhSdk\Api\LicenseApi;
use ChuckBartowski\OvhSdk\Api\LoadBalancerApi;
use ChuckBartowski\OvhSdk\Api\MeApi;
use ChuckBartowski\OvhSdk\Api\OrderApi;
use ChuckBartowski\OvhSdk\Api\SmsApi;
use ChuckBartowski\OvhSdk\Api\SupportApi;
use ChuckBartowski\OvhSdk\Api\VoipApi;
use ChuckBartowski\OvhSdk\Api\VpsApi;
use ChuckBartowski\OvhSdk\Api\VrackApi;
use ChuckBartowski\OvhSdk\Api\WebHostingApi;
use ChuckBartowski\OvhSdk\Client\OvhClient;

final class Ovh
{
    private array $apis = [];

    public function __construct(private readonly OvhClient $client)
    {
    }

    public function client(): OvhClient
    {
        return $this->client;
    }

    public function me(): MeApi
    {
        return $this->apis[MeApi::class] ??= new MeApi($this->client);
    }

    public function domains(): DomainApi
    {
        return $this->apis[DomainApi::class] ??= new DomainApi($this->client);
    }

    public function dedicatedServers(): DedicatedServerApi
    {
        return $this->apis[DedicatedServerApi::class] ??= new DedicatedServerApi($this->client);
    }

    public function vps(): VpsApi
    {
        return $this->apis[VpsApi::class] ??= new VpsApi($this->client);
    }

    public function cloud(): CloudApi
    {
        return $this->apis[CloudApi::class] ??= new CloudApi($this->client);
    }

    public function cloudDatabases(): CloudDatabaseApi
    {
        return $this->apis[CloudDatabaseApi::class] ??= new CloudDatabaseApi($this->client);
    }

    public function kubernetes(): KubernetesApi
    {
        return $this->apis[KubernetesApi::class] ??= new KubernetesApi($this->client);
    }

    public function webHosting(): WebHostingApi
    {
        return $this->apis[WebHostingApi::class] ??= new WebHostingApi($this->client);
    }

    public function email(): EmailApi
    {
        return $this->apis[EmailApi::class] ??= new EmailApi($this->client);
    }

    public function ips(): IpApi
    {
        return $this->apis[IpApi::class] ??= new IpApi($this->client);
    }

    public function vrack(): VrackApi
    {
        return $this->apis[VrackApi::class] ??= new VrackApi($this->client);
    }

    public function loadBalancers(): LoadBalancerApi
    {
        return $this->apis[LoadBalancerApi::class] ??= new LoadBalancerApi($this->client);
    }

    public function cdn(): CdnApi
    {
        return $this->apis[CdnApi::class] ??= new CdnApi($this->client);
    }

    public function licenses(): LicenseApi
    {
        return $this->apis[LicenseApi::class] ??= new LicenseApi($this->client);
    }

    public function orders(): OrderApi
    {
        return $this->apis[OrderApi::class] ??= new OrderApi($this->client);
    }

    public function sms(): SmsApi
    {
        return $this->apis[SmsApi::class] ??= new SmsApi($this->client);
    }

    public function dbaasLogs(): DbaasLogsApi
    {
        return $this->apis[DbaasLogsApi::class] ??= new DbaasLogsApi($this->client);
    }

    public function support(): SupportApi
    {
        return $this->apis[SupportApi::class] ??= new SupportApi($this->client);
    }

    public function voip(): VoipApi
    {
        return $this->apis[VoipApi::class] ??= new VoipApi($this->client);
    }
}
