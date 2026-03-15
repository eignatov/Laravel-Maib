<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Dto;

readonly class PaymentItem
{
    public function __construct(
        public string $id,
        public string $name,
        public float $price,
        public int $quantity,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'quantity' => $this->quantity,
        ];
    }
}
