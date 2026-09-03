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
        public ?TransactionStatus $status,
        public string $statusCode,
        public string $statusMessage,
        public ?ThreeDsStatus $threeDs,
        public ?string $rrn,
        public ?string $approval,
        public ?string $cardNumber,
        public float $amount,
        public ?Currency $currency,
        public string $signature,
    ) {}

    /**
     * Enum fields use tryFrom() so an undocumented value maib may add later
     * degrades to null instead of throwing — a signature-valid callback must
     * never 500, or maib would keep retrying it indefinitely.
     */
    public static function fromArray(array $data): self
    {
        $result = $data['result'] ?? $data;

        return new self(
            payId: $result['payId'],
            orderId: $result['orderId'] ?? null,
            billerId: $result['billerId'] ?? null,
            billerExpiry: $result['billerExpiry'] ?? null,
            status: isset($result['status']) ? TransactionStatus::tryFrom($result['status']) : null,
            statusCode: $result['statusCode'],
            statusMessage: $result['statusMessage'],
            threeDs: isset($result['threeDs']) ? ThreeDsStatus::tryFrom($result['threeDs']) : null,
            rrn: $result['rrn'] ?? null,
            approval: $result['approval'] ?? null,
            cardNumber: $result['cardNumber'] ?? null,
            amount: (float) $result['amount'],
            currency: isset($result['currency']) ? Currency::tryFrom($result['currency']) : null,
            signature: $data['signature'] ?? '',
        );
    }
}
