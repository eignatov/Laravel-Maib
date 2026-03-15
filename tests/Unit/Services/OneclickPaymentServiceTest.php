<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Tests\Unit\Services;

use Illuminate\Support\Facades\Http;
use Nikba\LaravelMaib\Dto\Requests\ExecuteOneclickRequest;
use Nikba\LaravelMaib\Dto\Requests\SaveCardOneclickRequest;
use Nikba\LaravelMaib\Dto\Responses\InitPaymentResponse;
use Nikba\LaravelMaib\Enums\Currency;
use Nikba\LaravelMaib\Enums\Language;
use Nikba\LaravelMaib\Services\OneclickPaymentService;
use Nikba\LaravelMaib\Tests\TestCase;

class OneclickPaymentServiceTest extends TestCase
{
    private const BILLER_ID = 'oneclick-biller-0001';

    public function test_save_card_flow(): void
    {
        Http::fake([
            'api.maibmerchants.md/v1/generate-token' => Http::response($this->fakeTokenResponse()),
            'api.maibmerchants.md/v1/savecard-oneclick' => Http::response($this->fakeInitPaymentResponse()),
        ]);

        $service = $this->app->make(OneclickPaymentService::class);
        $response = $service->saveCard(new SaveCardOneclickRequest(
            billerExpiry: '1228',
            clientIp: '127.0.0.1',
            currency: Currency::MDL,
            language: Language::EN,
            email: 'user@example.com',
        ));

        $this->assertInstanceOf(InitPaymentResponse::class, $response);
        $this->assertNotEmpty($response->payUrl);
    }

    public function test_execute_returns_pay_url_for_redirect(): void
    {
        Http::fake([
            'api.maibmerchants.md/v1/generate-token' => Http::response($this->fakeTokenResponse()),
            'api.maibmerchants.md/v1/execute-oneclick' => Http::response($this->fakeInitPaymentResponse()),
        ]);

        $service = $this->app->make(OneclickPaymentService::class);
        $response = $service->execute(new ExecuteOneclickRequest(
            billerId: self::BILLER_ID,
            amount: 49.99,
            currency: Currency::MDL,
            clientIp: '127.0.0.1',
            language: Language::RO,
        ));

        $this->assertInstanceOf(InitPaymentResponse::class, $response);
        $this->assertStringContainsString('maib.ecommerce.md', $response->payUrl);
    }
}
