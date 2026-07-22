<?php

declare(strict_types=1);

namespace ChuckBartowski\OvhSdk\Client;

use ChuckBartowski\OvhSdk\Exception\AuthenticationException;
use ChuckBartowski\OvhSdk\Exception\TransportException;
use ChuckBartowski\OvhSdk\Response\ApiResponse;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\RetryableHttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class OvhClient
{
    private const ENDPOINTS = [
        'ovh-eu' => 'https://eu.api.ovh.com/1.0',
        'ovh-ca' => 'https://ca.api.ovh.com/1.0',
        'ovh-us' => 'https://api.us.ovhcloud.com/1.0',
        'kimsufi-eu' => 'https://eu.api.kimsufi.com/1.0',
        'kimsufi-ca' => 'https://ca.api.kimsufi.com/1.0',
        'soyoustart-eu' => 'https://eu.api.soyoustart.com/1.0',
        'soyoustart-ca' => 'https://ca.api.soyoustart.com/1.0',
    ];

    private readonly string $baseUrl;

    private readonly HttpClientInterface $httpClient;

    private ?int $timeDelta = null;

    public function __construct(
        private readonly string $applicationKey,
        #[\SensitiveParameter]
        private readonly string $applicationSecret,
        #[\SensitiveParameter]
        private readonly ?string $consumerKey = null,
        string $endpoint = 'ovh-eu',
        private readonly float $timeout = 30.0,
        bool $retryFailed = false,
        int $maxRetries = 3,
        ?HttpClientInterface $httpClient = null,
    ) {
        $this->baseUrl = self::ENDPOINTS[$endpoint] ?? $endpoint;
        $client = $httpClient ?? HttpClient::create();
        $this->httpClient = $retryFailed ? new RetryableHttpClient($client, null, $maxRetries) : $client;
    }

    public function get(string $path, array $query = []): ApiResponse
    {
        return $this->request('GET', $path, $query);
    }

    public function post(string $path, array $body = []): ApiResponse
    {
        return $this->request('POST', $path, [], $body);
    }

    public function put(string $path, array $body = []): ApiResponse
    {
        return $this->request('PUT', $path, [], $body);
    }

    public function delete(string $path, array $query = []): ApiResponse
    {
        return $this->request('DELETE', $path, $query);
    }

    public function requestCredentials(array $accessRules, ?string $redirection = null): ApiResponse
    {
        if ('' === $this->applicationKey) {
            throw new AuthenticationException('Missing OVHcloud application key');
        }

        $body = json_encode(array_filter([
            'accessRules' => $accessRules,
            'redirection' => $redirection,
        ], static fn (mixed $v): bool => null !== $v), \JSON_THROW_ON_ERROR);

        try {
            $response = $this->httpClient->request('POST', $this->baseUrl.'/auth/credential', [
                'headers' => [
                    'X-Ovh-Application' => $this->applicationKey,
                    'Content-Type' => 'application/json',
                ],
                'body' => $body,
                'timeout' => $this->timeout,
            ]);

            return ApiResponse::fromHttp($response->getStatusCode(), $this->decode($response->getContent(false)));
        } catch (TransportExceptionInterface $e) {
            throw new TransportException($e->getMessage(), 0, $e);
        }
    }

    private function request(string $method, string $path, array $query = [], ?array $body = null): ApiResponse
    {
        if ('' === $this->applicationKey || '' === $this->applicationSecret) {
            throw new AuthenticationException('Missing OVHcloud application credentials');
        }

        if (null === $this->consumerKey || '' === $this->consumerKey) {
            throw new AuthenticationException('Missing OVHcloud consumer key: request one with requestCredentials()');
        }

        $url = $this->baseUrl.$path;

        if ([] !== $query) {
            $url .= '?'.http_build_query($query);
        }

        $bodyString = null !== $body ? json_encode($body, \JSON_THROW_ON_ERROR) : '';
        $timestamp = $this->now();
        $signature = '$1$'.sha1(implode('+', [
            $this->applicationSecret,
            $this->consumerKey,
            $method,
            $url,
            $bodyString,
            (string) $timestamp,
        ]));

        $options = [
            'headers' => [
                'X-Ovh-Application' => $this->applicationKey,
                'X-Ovh-Consumer' => $this->consumerKey,
                'X-Ovh-Timestamp' => (string) $timestamp,
                'X-Ovh-Signature' => $signature,
                'Content-Type' => 'application/json',
            ],
            'timeout' => $this->timeout,
        ];

        if (null !== $body) {
            $options['body'] = $bodyString;
        }

        try {
            $response = $this->httpClient->request($method, $url, $options);
            $statusCode = $response->getStatusCode();

            if (401 === $statusCode) {
                throw new AuthenticationException(sprintf('OVHcloud authentication failed (HTTP %d)', $statusCode));
            }

            $content = $response->getContent(false);
        } catch (TransportExceptionInterface $e) {
            throw new TransportException($e->getMessage(), 0, $e);
        }

        return ApiResponse::fromHttp($statusCode, $this->decode($content));
    }

    private function now(): int
    {
        if (null === $this->timeDelta) {
            $this->timeDelta = $this->fetchTimeDelta();
        }

        return time() + $this->timeDelta;
    }

    private function fetchTimeDelta(): int
    {
        try {
            $response = $this->httpClient->request('GET', $this->baseUrl.'/auth/time', ['timeout' => $this->timeout]);
            $serverTime = (int) trim($response->getContent(false));

            return 0 !== $serverTime ? $serverTime - time() : 0;
        } catch (TransportExceptionInterface) {
            return 0;
        }
    }

    private function decode(string $content): mixed
    {
        if ('' === $content) {
            return null;
        }

        try {
            return json_decode($content, true, 512, \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new TransportException('Invalid JSON response from the OVHcloud API', 0, $e);
        }
    }
}
