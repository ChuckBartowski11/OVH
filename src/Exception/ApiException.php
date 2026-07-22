<?php

declare(strict_types=1);

namespace ChuckBartowski\OvhSdk\Exception;

class ApiException extends \RuntimeException implements OvhSdkExceptionInterface
{
    public function __construct(
        private readonly array $errors,
        private readonly int $statusCode = 0,
        private readonly ?string $errorCode = null,
        private readonly mixed $raw = null,
    ) {
        parent::__construct(implode('; ', array_map('strval', $errors)) ?: 'OVHcloud API call failed');
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    public function getRaw(): mixed
    {
        return $this->raw;
    }
}
