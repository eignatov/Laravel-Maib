<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Dto\Requests;

readonly class RefundRequest
{
    public function __construct(
        public string $payId,
        public ?float $refundAmount = null,
    ) {}

    public function toArray(): array
    {
        $data = [
            'payId' => $this->payId,
        ];

        if ($this->refundAmount !== null) {
            $data['refundAmount'] = $this->refundAmount;
        }

        return $data;
    }
}
