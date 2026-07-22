<?php

declare(strict_types=1);

namespace ChuckBartowski\OvhSdk\Tests\Api;

use ChuckBartowski\OvhSdk\Client\OvhClient;
use ChuckBartowski\OvhSdk\Ovh;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\JsonMockResponse;
use Symfony\Component\HttpClient\Response\MockResponse;

final class FacadeTest extends TestCase
{
    private function ovh(callable $handler): Ovh
    {
        $http = new MockHttpClient(function (string $method, string $url, array $options) use ($handler) {
            if (str_ends_with($url, '/auth/time')) {
                return new MockResponse('1700000000');
            }

            return $handler($method, $url, $options);
        });

        return new Ovh(new OvhClient('K', 'S', 'C', 'ovh-eu', 30.0, false, 3, $http));
    }

    public function testDomainAddRecordPayloadAndPath(): void
    {
        $ovh = $this->ovh(function (string $method, string $url, array $options): JsonMockResponse {
            $this->assertSame('POST', $method);
            $this->assertStringEndsWith('/1.0/domain/zone/example.com/record', $url);
            $body = json_decode($options['body'], true);
            $this->assertSame('A', $body['fieldType']);
            $this->assertSame('www', $body['subDomain']);
            $this->assertSame('203.0.113.10', $body['target']);

            return new JsonMockResponse(['id' => 42]);
        });

        $ovh->domains()->addRecord('example.com', 'A', '203.0.113.10', 'www');
    }

    public function testCloudInstancePathBuilding(): void
    {
        $ovh = $this->ovh(function (string $method, string $url): JsonMockResponse {
            $this->assertStringEndsWith('/1.0/cloud/project/proj-123/instance', $url);

            return new JsonMockResponse([]);
        });

        $ovh->cloud()->instances('proj-123');
    }

    public function testKubernetesKubeconfigIsPost(): void
    {
        $ovh = $this->ovh(function (string $method, string $url): JsonMockResponse {
            $this->assertSame('POST', $method);
            $this->assertStringEndsWith('/1.0/cloud/project/p1/kube/c1/kubeconfig', $url);

            return new JsonMockResponse(['content' => 'apiVersion: v1']);
        });

        $ovh->kubernetes()->kubeconfig('p1', 'c1');
    }

    public function testIpSetReversePayload(): void
    {
        $ovh = $this->ovh(function (string $method, string $url, array $options): JsonMockResponse {
            $this->assertStringContainsString('/1.0/ip/', $url);
            $this->assertStringEndsWith('/reverse', $url);
            $body = json_decode($options['body'], true);
            $this->assertSame('mail.example.com', $body['reverse']);

            return new JsonMockResponse([]);
        });

        $ovh->ips()->setReverse('203.0.113.0/24', '203.0.113.10', 'mail.example.com');
    }

    public function testEmailCreateAccount(): void
    {
        $ovh = $this->ovh(function (string $method, string $url, array $options): JsonMockResponse {
            $this->assertStringEndsWith('/1.0/email/domain/example.com/account', $url);
            $body = json_decode($options['body'], true);
            $this->assertSame('support', $body['accountName']);

            return new JsonMockResponse(['id' => 1]);
        });

        $ovh->email()->createAccount('example.com', 'support', 'S3cret!');
    }

    public function testServiceNamesAreUrlEncoded(): void
    {
        $ovh = $this->ovh(function (string $method, string $url): JsonMockResponse {
            $this->assertStringContainsString('/1.0/vps/vps-123.vps.ovh.net', $url);

            return new JsonMockResponse([]);
        });

        $ovh->vps()->find('vps-123.vps.ovh.net');
    }

    public function testFacadeCachesApiInstances(): void
    {
        $ovh = $this->ovh(fn (): JsonMockResponse => new JsonMockResponse([]));

        $this->assertSame($ovh->domains(), $ovh->domains());
        $this->assertSame($ovh->cloud(), $ovh->cloud());
        $this->assertSame($ovh->me(), $ovh->me());
    }
}
