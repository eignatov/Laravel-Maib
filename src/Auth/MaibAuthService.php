<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Auth;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Nikba\LaravelMaib\Exceptions\MaibApiException;

class MaibAuthService
{
    private const ENDPOINT = '/generate-token';

    public function __construct(
        private readonly TokenCache $tokenCache,
        private readonly string $projectId,
        private readonly string $projectSecret,
        private readonly string $baseUrl,
    ) {}

    /**
     * Returns a valid access token, using cache when possible.
     * Falls back: cache → refresh token → full credentials.
     */
    public function getValidToken(): string
    {
        $accessToken = $this->tokenCache->getAccessToken();

        if ($accessToken !== null) {
            return $accessToken;
        }

        $refreshToken = $this->tokenCache->getRefreshToken();

        if ($refreshToken !== null) {
            try {
                $tokenData = $this->generateFromRefreshToken($refreshToken);
                $this->tokenCache->storeTokens($tokenData);

                return $tokenData['accessToken'];
            } catch (MaibApiException $e) {
                // Error 11002 means refresh token is invalid; fall through to credentials
                if ($e->errorCode !== '11002') {
                    throw $e;
                }
            }
        }

        $tokenData = $this->generateFromCredentials();
        $this->tokenCache->storeTokens($tokenData);

        return $tokenData['accessToken'];
    }

    /**
     * Clears cached tokens and forces re-authentication.
     */
    public function invalidate(): void
    {
        $this->tokenCache->clearTokens();
    }

    private function generateFromCredentials(): array
    {
        $response = Http::post($this->baseUrl.self::ENDPOINT, [
            'projectId' => $this->projectId,
            'projectSecret' => $this->projectSecret,
        ]);

        return $this->parseTokenResponse($response->json());
    }

    private function generateFromRefreshToken(string $refreshToken): array
    {
        $response = Http::post($this->baseUrl.self::ENDPOINT, [
            'refreshToken' => $refreshToken,
        ]);

        return $this->parseTokenResponse($response->json());
    }

    private function parseTokenResponse(?array $body): array
    {
        if (empty($body) || ($body['ok'] ?? false) === false) {
            $error = $body['errors'][0] ?? $body['error'] ?? [];
            Log::error('maib token generation failed', ['response' => $body]);
            throw MaibApiException::fromResponse($error);
        }

        return $body['result'];
    }
}
