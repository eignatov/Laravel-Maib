<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Dto\Requests;

use Nikba\LaravelMaib\Dto\PaymentItem;
use Nikba\LaravelMaib\Enums\Currency;

readonly class ExecuteRecurringRequest
{
    /**
     * No clientIp or language — server-side charge only.
     *
     * @param  PaymentItem[]  $items
     */
    public function __construct(
        public string $billerId,
        public float $amount,
        public Currency $currency,
        public ?string $description = null,
        public ?string $orderId = null,
        public ?float $delivery = null,
        public array $items = [],
    ) {}

    public function toArray(): array
    {
        $data = [
            'billerId' => $this->billerId,
            'amount' => $this->amount,
            'currency' => $this->currency->value,
        ];

        if ($this->description !== null) {
            $data['description'] = $this->description;
        }

        if ($this->orderId !== null) {
            $data['orderId'] = $this->orderId;
        }

        if ($this->delivery !== null) {
            $data['delivery'] = $this->delivery;
        }

        if ($this->items !== []) {
            $data['items'] = array_map(fn (PaymentItem $item) => $item->toArray(), $this->items);
        }

        return $data;
    }
}
