<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Dto\Requests;

use Nikba\LaravelMaib\Dto\PaymentItem;
use Nikba\LaravelMaib\Enums\Currency;
use Nikba\LaravelMaib\Enums\Language;

readonly class SaveCardOneclickRequest
{
    /**
     * @param  PaymentItem[]  $items
     */
    public function __construct(
        public string $billerExpiry,
        public string $clientIp,
        public Currency $currency,
        public Language $language,
        public string $email,
        public ?float $amount = null,
        public ?string $description = null,
        public ?string $clientName = null,
        public ?string $phone = null,
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
            'billerExpiry' => $this->billerExpiry,
            'clientIp' => $this->clientIp,
            'currency' => $this->currency->value,
            'language' => $this->language->value,
            'email' => $this->email,
        ];

        if ($this->amount !== null) {
            $data['amount'] = $this->amount;
        }

        if ($this->description !== null) {
            $data['description'] = $this->description;
        }

        if ($this->clientName !== null) {
            $data['clientName'] = $this->clientName;
        }

        if ($this->phone !== null) {
            $data['phone'] = $this->phone;
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
