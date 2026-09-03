<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Events;

use Nikba\LaravelMaib\Dto\Responses\CallbackPayload;

/**
 * Dispatched when a card is registered without an immediate charge
 * (savecard-recurring / savecard-oneclick without an amount).
 *
 * The callback returns status PENDING together with the billerId to store
 * for the customer — this is a successful tokenization, not a failure.
 */
class MaibCardSaved
{
    public function __construct(
        public readonly CallbackPayload $payload,
    ) {}
}
