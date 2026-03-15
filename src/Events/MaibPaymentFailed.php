<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Events;

use Nikba\LaravelMaib\Dto\Responses\CallbackPayload;

class MaibPaymentFailed
{
    public function __construct(
        public readonly CallbackPayload $payload,
    ) {}
}
