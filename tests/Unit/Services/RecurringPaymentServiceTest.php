<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Tests\Unit\Services;

use Illuminate\Support\Facades\Http;
use Nikba\LaravelMaib\Dto\Requests\ExecuteRecurringRequest;
use Nikba\LaravelMaib\Dto\Requests\SaveCardRecurringRequest;
use Nikba\LaravelMaib\Dto\Responses\ExecuteRecurringResponse;
use Nikba\LaravelMaib\Dto\Responses\InitPaymentResponse;
use Nikba\LaravelMaib\Enums\Currency;
use Nikba\LaravelMaib\Enums\Language;
use Nikba\LaravelMaib\Enums\TransactionStatus;
use Nikba\LaravelMaib\Services\RecurringPaymentService;
use Nikba\LaravelMaib\Tests\TestCase;

class RecurringPaymentServiceTest extends TestCase
{
    private const BILLER_ID = 'biller-uuid-0001';

    private function fakeExecuteRecurringResponse(): array
    {
        return [
            'result' => [
                'payId' => 'exec-recurring-pay-id',
                'billerId' => self::BILLER_ID,
                'orderId' => 'SUB-001',
                'status' => 'OK',
                'statusCode' => '000',
                'statusMessage' => 'Approved',
                'rrn' => '111122223333',
                'approval' => 'APP003',
                'cardNumber' => '411111****1111',
                'amount' => 29.99,
                'currency' => 'MDL',
            ],
            'ok' => true,
        ];
    }

    public function test_save_card_with_amount(): void
    {
        Http::fake([
            'api.maibmerchants.md/v1/generate-token' => Http::response($this->fakeTokenResponse()),
            'api.maibmerchants.md/v1/savecard-recurring' => Http::response($this->fakeInitPaymentResponse()),
        ]);

        $service = $this->app->make(RecurringPaymentService::class);
        $response = $service->saveCard(new SaveCardRecurringRequest(
            billerExpiry: '1228',
            clientIp: '127.0.0.1',
            currency: Currency::MDL,
            language: Language::RO,
            email: 'user@example.com',
            amount: 29.99,
        ));

        $this->assertInstanceOf(InitPaymentResponse::class, $response);
        $this->assertNotEmpty($response->payUrl);
    }

    public function test_save_card_without_amount_for_card_only(): void
    {
        $pendingResponse = $this->fakeInitPaymentResponse();
        // When no amount, a pending payUrl is still returned
        Http::fake([
            'api.maibmerchants.md/v1/generate-token' => Http::response($this->fakeTokenResponse()),
            'api.maibmerchants.md/v1/savecard-recurring' => Http::response($pendingResponse),
        ]);

        $service = $this->app->make(RecurringPaymentService::class);
        $response = $service->saveCard(new SaveCardRecurringRequest(
            billerExpiry: '1228',
            clientIp: '127.0.0.1',
            currency: Currency::MDL,
            language: Language::RO,
            email: 'user@example.com',
        ));

        $this->assertInstanceOf(InitPaymentResponse::class, $response);
    }

    public function test_execute_server_side_charge(): void
    {
        Http::fake([
            'api.maibmerchants.md/v1/generate-token' => Http::response($this->fakeTokenResponse()),
            'api.maibmerchants.md/v1/execute-recurring' => Http::response($this->fakeExecuteRecurringResponse()),
        ]);

        $service = $this->app->make(RecurringPaymentService::class);
        $response = $service->execute(new ExecuteRecurringRequest(
            billerId: self::BILLER_ID,
            amount: 29.99,
            currency: Currency::MDL,
            orderId: 'SUB-001',
        ));

        $this->assertInstanceOf(ExecuteRecurringResponse::class, $response);
        $this->assertSame(TransactionStatus::OK, $response->status);
        $this->assertSame(self::BILLER_ID, $response->billerId);
        $this->assertSame(29.99, $response->amount);
    }
}
