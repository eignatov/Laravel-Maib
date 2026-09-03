<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Tests;

use Illuminate\Support\Facades\Http;
use Nikba\LaravelMaib\MaibServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [MaibServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('maib.project_id', 'test-project-id');
        $app['config']->set('maib.project_secret', 'test-project-secret');
        $app['config']->set('maib.signature_key', 'test-signature-key');
        $app['config']->set('maib.base_url', 'https://api.maibmerchants.md/v1');
        $app['config']->set('maib.cache_driver', 'array');
        $app['config']->set('maib.callback_route', '/maib/callback');
        $app['config']->set('cache.default', 'array');
    }

    protected function fakeTokenResponse(
        string $accessToken = 'test-access-token',
        string $refreshToken = 'test-refresh-token',
        int $expiresIn = 300,
        int $refreshExpiresIn = 1800,
    ): array {
        return [
            'result' => [
                'accessToken' => $accessToken,
                'refreshToken' => $refreshToken,
                'expiresIn' => $expiresIn,
                'refreshExpiresIn' => $refreshExpiresIn,
                'tokenType' => 'Bearer',
            ],
            'ok' => true,
        ];
    }

    protected function fakeInitPaymentResponse(
        string $payId = 'f16a9006-128a-46bc-8e2a-77a6ee99df75',
        string $orderId = 'ORDER-001',
        string $payUrl = 'https://maib.ecommerce.md/ecomm01/ClientHandler?trans_id=test',
    ): array {
        return [
            'result' => [
                'payId' => $payId,
                'orderId' => $orderId,
                'payUrl' => $payUrl,
            ],
            'ok' => true,
        ];
    }

    protected function fakeCallbackPayload(
        string $payId = 'f16a9006-128a-46bc-8e2a-77a6ee99df75',
        string $status = 'OK',
        string $signatureKey = 'test-signature-key',
        ?string $billerId = null,
    ): array {
        $result = [
            'amount' => 99.99,
            'approval' => 'TEST01',
            'cardNumber' => '411111****1111',
            'currency' => 'MDL',
            'orderId' => 'ORDER-001',
            'payId' => $payId,
            'rrn' => '123456789012',
            'status' => $status,
            'statusCode' => '000',
            'statusMessage' => 'Approved',
        ];

        if ($billerId !== null) {
            $result['billerId'] = $billerId;
        }

        $sorted = $result;
        ksort($sorted);
        $sorted[] = $signatureKey;
        $string = implode(':', array_values($sorted));
        $signature = base64_encode(hash('sha256', $string, true));

        return [
            'result' => $result,
            'signature' => $signature,
        ];
    }

    protected function fakeApiErrorResponse(
        string $errorCode = '10001',
        string $errorMessage = 'Unauthorized',
    ): array {
        return [
            'errors' => [
                [
                    'errorCode' => $errorCode,
                    'errorMessage' => $errorMessage,
                ],
            ],
            'ok' => false,
        ];
    }

    protected function stubTokenEndpoint(): void
    {
        Http::fake([
            'api.maibmerchants.md/v1/generate-token' => Http::response(
                $this->fakeTokenResponse()
            ),
        ]);
    }
}
