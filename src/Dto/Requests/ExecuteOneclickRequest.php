<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Dto\Requests;

use Nikba\LaravelMaib\Dto\PaymentItem;
use Nikba\LaravelMaib\Enums\Currency;
use Nikba\LaravelMaib\Enums\Language;

readonly class ExecuteOneclickRequest
{
    /**
     * clientIp and language are required — redirects customer to checkout for 3DS.
     *
     * @param  PaymentItem[]  $items
     */
    public function __construct(
        public string $billerId,
        public float $amount,
        public Currency $currency,
        public string $clientIp,
        public Language $language,
        public ?string $description = null,
        public ?string $orderId = null,
        public ?float $delivery = null,
        public array $items = [],
        public ?string $callbackUrl = null,
        public ?string $okUrl = null,
        public ?string $failUrl = null,
    ) {}

    public function toArray(): array
    {
        $data = [
            'billerId' => $this->billerId,
            'amount' => $this->amount,
            'currency' => $this->currency->value,
            'clientIp' => $this->clientIp,
            'language' => $this->language->value,
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

        if ($this->callbackUrl !== null) {
            $data['callbackUrl'] = $this->callbackUrl;
        }

        if ($this->okUrl !== null) {
            $data['okUrl'] = $this->okUrl;
        }

        if ($this->failUrl !== null) {
            $data['failUrl'] = $this->failUrl;
        }

        return $data;
    }
}
