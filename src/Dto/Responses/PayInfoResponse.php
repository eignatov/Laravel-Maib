<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Dto\Responses;

use Nikba\LaravelMaib\Dto\PaymentItem;
use Nikba\LaravelMaib\Enums\Currency;
use Nikba\LaravelMaib\Enums\ThreeDsStatus;
use Nikba\LaravelMaib\Enums\TransactionStatus;

readonly class PayInfoResponse
{
    /**
     * @param  PaymentItem[]  $items
     */
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
        public ?float $confirmAmount,
        public ?float $refundAmount,
        public Currency $currency,
        public ?string $description,
        public ?string $clientIp,
        public ?string $clientName,
        public ?string $email,
        public ?string $phone,
        public ?float $delivery,
        public array $items,
    ) {}

    public static function fromArray(array $data): self
    {
        $result = $data['result'] ?? $data;

        $threeDs = isset($result['threeDs'])
            ? ThreeDsStatus::tryFrom($result['threeDs'])
            : null;

        $items = [];
        foreach ($result['items'] ?? [] as $item) {
            $items[] = new PaymentItem(
                id: (string) $item['id'],
                name: $item['name'],
                price: (float) $item['price'],
                quantity: (int) $item['quantity'],
            );
        }

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
            confirmAmount: isset($result['confirmAmount']) ? (float) $result['confirmAmount'] : null,
            refundAmount: isset($result['refundAmount']) ? (float) $result['refundAmount'] : null,
            currency: Currency::from($result['currency']),
            description: $result['description'] ?? null,
            clientIp: $result['clientIp'] ?? null,
            clientName: $result['clientName'] ?? null,
            email: $result['email'] ?? null,
            phone: $result['phone'] ?? null,
            delivery: isset($result['delivery']) ? (float) $result['delivery'] : null,
            items: $items,
        );
    }
}
