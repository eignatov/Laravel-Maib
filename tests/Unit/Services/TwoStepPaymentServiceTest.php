<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Tests\Unit\Services;

use Illuminate\Support\Facades\Http;
use Nikba\LaravelMaib\Dto\Requests\CapturePaymentRequest;
use Nikba\LaravelMaib\Dto\Requests\HoldPaymentRequest;
use Nikba\LaravelMaib\Dto\Requests\RefundRequest;
use Nikba\LaravelMaib\Dto\Responses\CaptureResponse;
use Nikba\LaravelMaib\Dto\Responses\InitPaymentResponse;
use Nikba\LaravelMaib\Dto\Responses\RefundResponse;
use Nikba\LaravelMaib\Enums\Currency;
use Nikba\LaravelMaib\Enums\Language;
use Nikba\LaravelMaib\Enums\TransactionStatus;
use Nikba\LaravelMaib\Services\RefundService;
use Nikba\LaravelMaib\Services\TwoStepPaymentService;
use Nikba\LaravelMaib\Tests\TestCase;

class TwoStepPaymentServiceTest extends TestCase
{
    private const PAY_ID = 'hold-pay-id-001';

    private function fakeCaptureResponse(float $confirmAmount = 99.99): array
    {
        return [
            'result' => [
                'payId' => self::PAY_ID,
                'orderId' => 'ORDER-002',
                'status' => 'OK',
                'statusCode' => '000',
                'statusMessage' => 'Approved',
                'rrn' => '987654321098',
                'approval' => 'APP002',
                'cardNumber' => '411111****1111',
                'confirmAmount' => $confirmAmount,
            ],
            'ok' => true,
        ];
    }

    private function fakeRefundResponse(float $refundAmount = 99.99): array
    {
        return [
            'result' => [
                'payId' => self::PAY_ID,
                'orderId' => 'ORDER-002',
                'status' => 'REVERSED',
                'statusCode' => '000',
                'statusMessage' => 'Reversed',
                'refundAmount' => $refundAmount,
            ],
            'ok' => true,
        ];
    }

    public function test_hold_returns_pay_url(): void
    {
        Http::fake([
            'api.maibmerchants.md/v1/generate-token' => Http::response($this->fakeTokenResponse()),
            'api.maibmerchants.md/v1/hold' => Http::response(
                $this->fakeInitPaymentResponse(self::PAY_ID, 'ORDER-002')
            ),
        ]);

        $service = $this->app->make(TwoStepPaymentService::class);
        $response = $service->hold(new HoldPaymentRequest(
            amount: 99.99,
            currency: Currency::MDL,
            clientIp: '127.0.0.1',
            language: Language::EN,
        ));

        $this->assertInstanceOf(InitPaymentResponse::class, $response);
        $this->assertSame(self::PAY_ID, $response->payId);
        $this->assertNotEmpty($response->payUrl);
    }

    public function test_capture_with_full_amount(): void
    {
        Http::fake([
            'api.maibmerchants.md/v1/generate-token' => Http::response($this->fakeTokenResponse()),
            'api.maibmerchants.md/v1/complete' => Http::response($this->fakeCaptureResponse(99.99)),
        ]);

        $service = $this->app->make(TwoStepPaymentService::class);
        $response = $service->capture(new CapturePaymentRequest(payId: self::PAY_ID));

        $this->assertInstanceOf(CaptureResponse::class, $response);
        $this->assertSame(TransactionStatus::OK, $response->status);
        $this->assertSame(99.99, $response->confirmAmount);
    }

    public function test_capture_with_partial_amount(): void
    {
        Http::fake([
            'api.maibmerchants.md/v1/generate-token' => Http::response($this->fakeTokenResponse()),
            'api.maibmerchants.md/v1/complete' => Http::response($this->fakeCaptureResponse(50.00)),
        ]);

        $service = $this->app->make(TwoStepPaymentService::class);
        $response = $service->capture(new CapturePaymentRequest(
            payId: self::PAY_ID,
            confirmAmount: 50.00,
        ));

        $this->assertSame(50.00, $response->confirmAmount);
    }

    public function test_refund_on_held_transaction(): void
    {
        Http::fake([
            'api.maibmerchants.md/v1/generate-token' => Http::response($this->fakeTokenResponse()),
            'api.maibmerchants.md/v1/refund' => Http::response($this->fakeRefundResponse(99.99)),
        ]);

        $service = $this->app->make(RefundService::class);
        $response = $service->refund(new RefundRequest(payId: self::PAY_ID));

        $this->assertInstanceOf(RefundResponse::class, $response);
        $this->assertSame(TransactionStatus::REVERSED, $response->status);
        $this->assertSame(99.99, $response->refundAmount);
    }
}
