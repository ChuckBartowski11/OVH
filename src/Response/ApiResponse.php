<?php

declare(strict_types=1);

namespace ChuckBartowski\OvhSdk\Response;

use ChuckBartowski\OvhSdk\Exception\ApiException;
use ChuckBartowski\OvhSdk\Exception\ConflictException;
use ChuckBartowski\OvhSdk\Exception\ForbiddenException;
use ChuckBartowski\OvhSdk\Exception\InvalidRequestException;
use ChuckBartowski\OvhSdk\Exception\RateLimitException;
use ChuckBartowski\OvhSdk\Exception\ResourceNotFoundException;

final readonly class ApiResponse
{
    public function __construct(
        public bool $success,
        public int $statusCode,
        public mixed $data,
        public array $errors,
        public ?string $errorCode,
        public mixed $raw,
    ) {
    }

    public static function fromHttp(int $statusCode, mixed $payload): self
    {
        $success = $statusCode >= 200 && $statusCode < 300;
        $errors = [];
        $errorCode = null;

        if (!$success) {
            if (\is_array($payload) && isset($payload['message'])) {
                $errorCode = isset($payload['errorCode']) ? (string) $payload['errorCode'] : null;
                $errors[] = null !== $errorCode
                    ? sprintf('%s: %s', $errorCode, $payload['message'])
                    : (string) $payload['message'];
            } else {
                $errors = [sprintf('HTTP %d', $statusCode)];
            }
        }

        return new self($success, $statusCode, $payload, $errors, $errorCode, $payload);
    }

    public function ensureSuccess(): self
    {
        if ($this->success) {
            return $this;
        }

        $errors = $this->errors ?: ['OVHcloud API call failed'];

        throw match (true) {
            404 === $this->statusCode => new ResourceNotFoundException($errors, 404, $this->errorCode, $this->raw),
            403 === $this->statusCode => new ForbiddenException($errors, 403, $this->errorCode, $this->raw),
            409 === $this->statusCode => new ConflictException($errors, 409, $this->errorCode, $this->raw),
            429 === $this->statusCode => new RateLimitException($errors, 429, $this->errorCode, $this->raw),
            400 === $this->statusCode => new InvalidRequestException($errors, 400, $this->errorCode, $this->raw),
            default => new ApiException($errors, $this->statusCode, $this->errorCode, $this->raw),
        };
    }

    public function data(?string $key = null, mixed $default = null): mixed
    {
        if (null === $key) {
            return $this->data;
        }

        return \is_array($this->data) ? ($this->data[$key] ?? $default) : $default;
    }

    public function items(): array
    {
        return \is_array($this->data) ? array_values($this->data) : [];
    }

    public function first(): mixed
    {
        return $this->items()[0] ?? null;
    }

    public function as(string $model): ?object
    {
        return \is_array($this->data) && !array_is_list($this->data) ? $model::from($this->data) : null;
    }

    public function asList(string $model): array
    {
        return array_map(
            static fn (array $item): object => $model::from($item),
            array_values(array_filter($this->items(), 'is_array')),
        );
    }
}
