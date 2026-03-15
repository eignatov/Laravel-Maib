<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Tests\Unit\Services;

use Illuminate\Support\Facades\Http;
use Nikba\LaravelMaib\Dto\Responses\PayInfoResponse;
use Nikba\LaravelMaib\Enums\TransactionStatus;
use Nikba\LaravelMaib\Services\PayInfoService;
use Nikba\LaravelMaib\Tests\TestCase;

class PayInfoServiceTest extends TestCase
{
    private const PAY_ID = 'info-pay-id-001';

    private function fakePayInfoResponse(): array
    {
        return [
            'result' => [
                'payId' => self::PAY_ID,
                'orderId' => 'ORDER-INFO',
                'status' => 'OK',
                'statusCode' => '000',
                'statusMessage' => 'Approved',
                'amount' => 75.50,
                'currency' => 'MDL',
                'cardNumber' => '411111****1111',
                'rrn' => '000011112222',
                'approval' => 'APP005',
            ],
            'ok' => true,
        ];
    }

    public function test_get_returns_pay_info_response(): void
    {
        Http::fake([
            'api.maibmerchants.md/v1/generate-token' => Http::response($this->fakeTokenResponse()),
            'api.maibmerchants.md/v1/pay-info/'.self::PAY_ID => Http::response($this->fakePayInfoResponse()),
        ]);

        $service = $this->app->make(PayInfoService::class);
        $response = $service->get(self::PAY_ID);

        $this->assertInstanceOf(PayInfoResponse::class, $response);
        $this->assertSame(self::PAY_ID, $response->payId);
        $this->assertSame(TransactionStatus::OK, $response->status);
        $this->assertSame(75.50, $response->amount);
    }
}
