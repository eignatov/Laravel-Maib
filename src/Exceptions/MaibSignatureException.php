<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Exceptions;

use RuntimeException;
use Throwable;

class MaibSignatureException extends RuntimeException
{
    public function __construct(
        string $message = 'Invalid maib callback signature.',
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
