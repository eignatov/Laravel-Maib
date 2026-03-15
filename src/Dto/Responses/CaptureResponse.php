<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Dto\Responses;

use Nikba\LaravelMaib\Enums\TransactionStatus;

readonly class CaptureResponse
{
    public function __construct(
        public string $payId,
        public ?string $orderId,
        public TransactionStatus $status,
        public string $statusCode,
        public string $statusMessage,
        public ?string $rrn,
        public ?string $approval,
        public ?string $cardNumber,
        public float $confirmAmount,
    ) {}

    public static function fromArray(array $data): self
    {
        $result = $data['result'] ?? $data;

        return new self(
            payId: $result['payId'],
            orderId: $result['orderId'] ?? null,
            status: TransactionStatus::from($result['status']),
            statusCode: $result['statusCode'],
            statusMessage: $result['statusMessage'],
            rrn: $result['rrn'] ?? null,
            approval: $result['approval'] ?? null,
            cardNumber: $result['cardNumber'] ?? null,
            confirmAmount: (float) $result['confirmAmount'],
        );
    }
}
