<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Dto\Responses;

use Nikba\LaravelMaib\Enums\Currency;
use Nikba\LaravelMaib\Enums\ThreeDsStatus;
use Nikba\LaravelMaib\Enums\TransactionStatus;

readonly class CallbackPayload
{
    public function __construct(
        public string $payId,
        public ?string $orderId,
        public ?string $billerId,
        public ?string $billerExpiry,
        public TransactionStatus $status,
        public string $statusCode,
        public string $statusMessage,
        public ?ThreeDsStatus $threeDs,
        public ?string $rrn,
        public ?string $approval,
        public ?string $cardNumber,
        public float $amount,
        public Currency $currency,
        public string $signature,
    ) {}

    public static function fromArray(array $data): self
    {
        $result = $data['result'] ?? $data;

        $threeDs = isset($result['threeDs'])
            ? ThreeDsStatus::from($result['threeDs'])
            : null;

        return new self(
            payId: $result['payId'],
            orderId: $result['orderId'] ?? null,
            billerId: $result['billerId'] ?? null,
            billerExpiry: $result['billerExpiry'] ?? null,
            status: TransactionStatus::from($result['status']),
            statusCode: $result['statusCode'],
            statusMessage: $result['statusMessage'],
            threeDs: $threeDs,
            rrn: $result['rrn'] ?? null,
            approval: $result['approval'] ?? null,
            cardNumber: $result['cardNumber'] ?? null,
            amount: (float) $result['amount'],
            currency: Currency::from($result['currency']),
            signature: $data['signature'],
        );
    }
}
