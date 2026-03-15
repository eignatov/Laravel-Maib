<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Tests\Unit\Services;

use Illuminate\Support\Facades\Http;
use Nikba\LaravelMaib\Dto\Requests\DirectPaymentRequest;
use Nikba\LaravelMaib\Dto\Responses\InitPaymentResponse;
use Nikba\LaravelMaib\Enums\Currency;
use Nikba\LaravelMaib\Enums\Language;
use Nikba\LaravelMaib\Exceptions\MaibApiException;
use Nikba\LaravelMaib\Services\DirectPaymentService;
use Nikba\LaravelMaib\Tests\TestCase;

class DirectPaymentServiceTest extends TestCase
{
    private function fakeToken(): void
    {
        Http::fake([
            'api.maibmerchants.md/v1/generate-token' => Http::response(
                $this->fakeTokenResponse()
            ),
        ]);
    }

    public function test_successful_initiation_returns_init_payment_response(): void
    {
        Http::fake([
            'api.maibmerchants.md/v1/generate-token' => Http::response($this->fakeTokenResponse()),
            'api.maibmerchants.md/v1/pay' => Http::response($this->fakeInitPaymentResponse()),
        ]);

        $service = $this->app->make(DirectPaymentService::class);
        $response = $service->initiate(new DirectPaymentRequest(
            amount: 99.99,
            currency: Currency::MDL,
            clientIp: '127.0.0.1',
            language: Language::RO,
            orderId: 'ORDER-001',
        ));

        $this->assertInstanceOf(InitPaymentResponse::class, $response);
        $this->assertSame('f16a9006-128a-46bc-8e2a-77a6ee99df75', $response->payId);
        $this->assertStringContainsString('maib.ecommerce.md', $response->payUrl);
    }

    public function test_api_error_throws_maib_api_exception(): void
    {
        Http::fake([
            'api.maibmerchants.md/v1/generate-token' => Http::response($this->fakeTokenResponse()),
            'api.maibmerchants.md/v1/pay' => Http::response($this->fakeApiErrorResponse('20001', 'Invalid amount.')),
        ]);

        $service = $this->app->make(DirectPaymentService::class);

        $this->expectException(MaibApiException::class);

        $service->initiate(new DirectPaymentRequest(
            amount: -1,
            currency: Currency::MDL,
            clientIp: '127.0.0.1',
            language: Language::RO,
        ));
    }
}
