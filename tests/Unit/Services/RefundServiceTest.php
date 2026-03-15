<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Tests\Unit\Services;

use Illuminate\Support\Facades\Http;
use Nikba\LaravelMaib\Dto\Requests\RefundRequest;
use Nikba\LaravelMaib\Dto\Responses\RefundResponse;
use Nikba\LaravelMaib\Enums\TransactionStatus;
use Nikba\LaravelMaib\Services\RefundService;
use Nikba\LaravelMaib\Tests\TestCase;

class RefundServiceTest extends TestCase
{
    private const PAY_ID = 'refund-pay-id-001';

    private function fakeRefundResponse(float $amount, string $status = 'REVERSED'): array
    {
        return [
            'result' => [
                'payId' => self::PAY_ID,
                'orderId' => 'ORDER-REFUND',
                'status' => $status,
                'statusCode' => '000',
                'statusMessage' => 'Reversed',
                'refundAmount' => $amount,
            ],
            'ok' => true,
        ];
    }

    public function test_full_refund(): void
    {
        Http::fake([
            'api.maibmerchants.md/v1/generate-token' => Http::response($this->fakeTokenResponse()),
            'api.maibmerchants.md/v1/refund' => Http::response($this->fakeRefundResponse(100.00)),
        ]);

        $service = $this->app->make(RefundService::class);
        $response = $service->refund(new RefundRequest(payId: self::PAY_ID));

        $this->assertInstanceOf(RefundResponse::class, $response);
        $this->assertSame(TransactionStatus::REVERSED, $response->status);
        $this->assertSame(100.00, $response->refundAmount);
    }

    public function test_partial_refund(): void
    {
        Http::fake([
            'api.maibmerchants.md/v1/generate-token' => Http::response($this->fakeTokenResponse()),
            'api.maibmerchants.md/v1/refund' => Http::response($this->fakeRefundResponse(30.00)),
        ]);

        $service = $this->app->make(RefundService::class);
        $response = $service->refund(new RefundRequest(
            payId: self::PAY_ID,
            refundAmount: 30.00,
        ));

        $this->assertSame(30.00, $response->refundAmount);
    }
}
