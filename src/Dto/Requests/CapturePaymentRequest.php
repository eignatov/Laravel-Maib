<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Dto\Requests;

readonly class CapturePaymentRequest
{
    public function __construct(
        public string $payId,
        public ?float $confirmAmount = null,
    ) {}

    public function toArray(): array
    {
        $data = [
            'payId' => $this->payId,
        ];

        if ($this->confirmAmount !== null) {
            $data['confirmAmount'] = $this->confirmAmount;
        }

        return $data;
    }
}
