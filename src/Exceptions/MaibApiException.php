<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Exceptions;

use RuntimeException;
use Throwable;

class MaibApiException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly string $errorCode,
        public readonly ?array $errorArgs = null,
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public static function fromResponse(array $error): self
    {
        $errorCode = (string) ($error['errorCode'] ?? $error['code'] ?? 'UNKNOWN');
        $errorMessage = $error['errorMessage'] ?? $error['message'] ?? 'Unknown API error.';
        $errorArgs = $error['errorArgs'] ?? null;

        return new self($errorMessage, $errorCode, $errorArgs);
    }
}
