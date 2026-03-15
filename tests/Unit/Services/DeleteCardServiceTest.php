<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Tests\Unit\Services;

use Illuminate\Support\Facades\Http;
use Nikba\LaravelMaib\Dto\Responses\DeleteCardResponse;
use Nikba\LaravelMaib\Services\DeleteCardService;
use Nikba\LaravelMaib\Tests\TestCase;

class DeleteCardServiceTest extends TestCase
{
    private const BILLER_ID = 'delete-biller-0001';

    private function fakeDeleteCardResponse(): array
    {
        return [
            'result' => [
                'billerId' => self::BILLER_ID,
                'status' => 'OK',
            ],
            'ok' => true,
        ];
    }

    public function test_delete_returns_delete_card_response(): void
    {
        Http::fake([
            'api.maibmerchants.md/v1/generate-token' => Http::response($this->fakeTokenResponse()),
            'api.maibmerchants.md/v1/delete-card/'.self::BILLER_ID => Http::response($this->fakeDeleteCardResponse()),
        ]);

        $service = $this->app->make(DeleteCardService::class);
        $response = $service->delete(self::BILLER_ID);

        $this->assertInstanceOf(DeleteCardResponse::class, $response);
        $this->assertSame(self::BILLER_ID, $response->billerId);
        $this->assertSame('OK', $response->status);
    }
}
