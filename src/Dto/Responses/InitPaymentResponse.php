<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Dto\Responses;

readonly class InitPaymentResponse
{
    public function __construct(
        public string $payId,
        public ?string $orderId,
        public string $payUrl,
    ) {}

    public static function fromArray(array $data): self
    {
        $result = $data['result'] ?? $data;

        return new self(
            payId: $result['payId'],
            orderId: $result['orderId'] ?? null,
            payUrl: $result['payUrl'],
        );
    }
}
