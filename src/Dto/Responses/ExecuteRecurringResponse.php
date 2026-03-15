<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Dto\Responses;

use Nikba\LaravelMaib\Enums\Currency;
use Nikba\LaravelMaib\Enums\TransactionStatus;

readonly class ExecuteRecurringResponse
{
    public function __construct(
        public string $payId,
        public string $billerId,
        public ?string $orderId,
        public TransactionStatus $status,
        public string $statusCode,
        public string $statusMessage,
        public ?string $rrn,
        public ?string $approval,
        public ?string $cardNumber,
        public float $amount,
        public Currency $currency,
    ) {}

    public static function fromArray(array $data): self
    {
        $result = $data['result'] ?? $data;

        return new self(
            payId: $result['payId'],
            billerId: $result['billerId'],
            orderId: $result['orderId'] ?? null,
            status: TransactionStatus::from($result['status']),
            statusCode: $result['statusCode'],
            statusMessage: $result['statusMessage'],
            rrn: $result['rrn'] ?? null,
            approval: $result['approval'] ?? null,
            cardNumber: $result['cardNumber'] ?? null,
            amount: (float) $result['amount'],
            currency: Currency::from($result['currency']),
        );
    }
}
