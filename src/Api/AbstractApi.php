<?php

declare(strict_types=1);

namespace ChuckBartowski\OvhSdk\Api;

use ChuckBartowski\OvhSdk\Client\OvhClient;
use ChuckBartowski\OvhSdk\Response\ApiResponse;

abstract class AbstractApi
{
    public function __construct(protected readonly OvhClient $client)
    {
    }

    protected function get(string $path, array $query = []): ApiResponse
    {
        return $this->client->get($path, $query)->ensureSuccess();
    }

    protected function post(string $path, array $body = []): ApiResponse
    {
        return $this->client->post($path, $body)->ensureSuccess();
    }

    protected function put(string $path, array $body = []): ApiResponse
    {
        return $this->client->put($path, $body)->ensureSuccess();
    }

    protected function delete(string $path, array $query = []): ApiResponse
    {
        return $this->client->delete($path, $query)->ensureSuccess();
    }

    protected function encode(string $segment): string
    {
        return rawurlencode($segment);
    }
}
