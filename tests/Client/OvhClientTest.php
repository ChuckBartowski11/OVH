<?php

declare(strict_types=1);

namespace ChuckBartowski\OvhSdk\Tests\Client;

use ChuckBartowski\OvhSdk\Client\OvhClient;
use ChuckBartowski\OvhSdk\Exception\ApiException;
use ChuckBartowski\OvhSdk\Exception\AuthenticationException;
use ChuckBartowski\OvhSdk\Exception\ResourceNotFoundException;
use ChuckBartowski\OvhSdk\Exception\TransportException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\JsonMockResponse;
use Symfony\Component\HttpClient\Response\MockResponse;

final class OvhClientTest extends TestCase
{
    private function timeThen(callable $handler): MockHttpClient
    {
        return new MockHttpClient(function (string $method, string $url, array $options) use ($handler): MockResponse {
            if (str_ends_with($url, '/auth/time')) {
                return new MockResponse('1700000000');
            }

            return $handler($method, $url, $options);
        });
    }

    public function testSignsRequestWithExpectedScheme(): void
    {
        $captured = [];
        $http = $this->timeThen(function (string $method, string $url, array $options) use (&$captured): JsonMockResponse {
            $captured = ['method' => $method, 'url' => $url, 'headers' => $options['headers']];

            return new JsonMockResponse(['firstname' => 'Chuck']);
        });

        $client = new OvhClient('APPKEY', 'APPSECRET', 'CONSUMER', 'ovh-eu', 30.0, false, 3, $http);
        $response = $client->get('/me');

        $this->assertTrue($response->success);
        $this->assertSame('https://eu.api.ovh.com/1.0/me', $captured['url']);

        $headers = [];
        foreach ($captured['headers'] as $line) {
            [$name, $value] = explode(': ', $line, 2);
            $headers[strtolower($name)] = $value;
        }

        $this->assertSame('APPKEY', $headers['x-ovh-application']);
        $this->assertSame('CONSUMER', $headers['x-ovh-consumer']);

        $timestamp = $headers['x-ovh-timestamp'];
        $expected = '$1$'.sha1(implode('+', ['APPSECRET', 'CONSUMER', 'GET', 'https://eu.api.ovh.com/1.0/me', '', $timestamp]));
        $this->assertSame($expected, $headers['x-ovh-signature']);
    }

    public function testSignatureCoversBodyOnPost(): void
    {
        $captured = [];
        $http = $this->timeThen(function (string $method, string $url, array $options) use (&$captured): JsonMockResponse {
            $captured = ['url' => $url, 'body' => $options['body'], 'headers' => $options['headers']];

            return new JsonMockResponse(['id' => 1], ['http_code' => 200]);
        });

        $client = new OvhClient('APPKEY', 'APPSECRET', 'CONSUMER', 'ovh-eu', 30.0, false, 3, $http);
        $client->post('/domain/zone/example.com/record', ['fieldType' => 'A', 'subDomain' => 'www', 'target' => '203.0.113.10']);

        $headers = [];
        foreach ($captured['headers'] as $line) {
            [$name, $value] = explode(': ', $line, 2);
            $headers[strtolower($name)] = $value;
        }

        $expected = '$1$'.sha1(implode('+', [
            'APPSECRET',
            'CONSUMER',
            'POST',
            'https://eu.api.ovh.com/1.0/domain/zone/example.com/record',
            $captured['body'],
            $headers['x-ovh-timestamp'],
        ]));
        $this->assertSame($expected, $headers['x-ovh-signature']);
    }

    public function testEndpointSelection(): void
    {
        $captured = '';
        $http = $this->timeThen(function (string $method, string $url) use (&$captured): JsonMockResponse {
            $captured = $url;

            return new JsonMockResponse([]);
        });

        (new OvhClient('K', 'S', 'C', 'soyoustart-eu', 30.0, false, 3, $http))->get('/dedicated/server');

        $this->assertStringStartsWith('https://eu.api.soyoustart.com/1.0/dedicated/server', $captured);
    }

    public function testMissingConsumerKeyThrowsBeforeRequest(): void
    {
        $client = new OvhClient('K', 'S', null, 'ovh-eu', 30.0, false, 3, new MockHttpClient());

        $this->expectException(AuthenticationException::class);
        $client->get('/me');
    }

    public function testRequestCredentialsOnlySendsApplicationHeader(): void
    {
        $captured = [];
        $http = new MockHttpClient(function (string $method, string $url, array $options) use (&$captured): JsonMockResponse {
            $captured = ['url' => $url, 'headers' => $options['headers'], 'body' => $options['body']];

            return new JsonMockResponse(['consumerKey' => 'NEWCK', 'validationUrl' => 'https://ovh.com/auth/x'], ['http_code' => 200]);
        });

        $client = new OvhClient('APPKEY', 'APPSECRET', null, 'ovh-eu', 30.0, false, 3, $http);
        $response = $client->requestCredentials([['method' => 'GET', 'path' => '/*']], 'https://app.example.com/callback');

        $this->assertSame('NEWCK', $response->data('consumerKey'));
        $this->assertStringEndsWith('/auth/credential', $captured['url']);
        $joined = implode("\n", $captured['headers']);
        $this->assertStringContainsString('X-Ovh-Application: APPKEY', $joined);
        $this->assertStringNotContainsString('X-Ovh-Signature', $joined);
    }

    public function testNotFoundThrowsResourceNotFoundException(): void
    {
        $http = $this->timeThen(fn (): JsonMockResponse => new JsonMockResponse(
            ['message' => 'This service does not exist', 'errorCode' => 'NOT_FOUND'],
            ['http_code' => 404],
        ));

        $client = new OvhClient('K', 'S', 'C', 'ovh-eu', 30.0, false, 3, $http);

        $this->expectException(ResourceNotFoundException::class);
        $client->get('/vps/unknown')->ensureSuccess();
    }

    public function testErrorExposesMessageAndCode(): void
    {
        $http = $this->timeThen(fn (): JsonMockResponse => new JsonMockResponse(
            ['message' => 'Invalid signature', 'errorCode' => 'INVALID_SIGNATURE'],
            ['http_code' => 400],
        ));

        $response = (new OvhClient('K', 'S', 'C', 'ovh-eu', 30.0, false, 3, $http))->get('/me');

        $this->assertFalse($response->success);
        $this->assertSame('INVALID_SIGNATURE', $response->errorCode);
        $this->assertSame(['INVALID_SIGNATURE: Invalid signature'], $response->errors);
        $this->expectException(ApiException::class);
        $response->ensureSuccess();
    }

    public function testInvalidJsonThrowsTransportException(): void
    {
        $http = $this->timeThen(fn (): MockResponse => new MockResponse('<html>gateway</html>', ['http_code' => 200]));

        $this->expectException(TransportException::class);
        (new OvhClient('K', 'S', 'C', 'ovh-eu', 30.0, false, 3, $http))->get('/me');
    }
}
