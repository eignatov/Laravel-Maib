<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Tests\Unit\Auth;

use Illuminate\Support\Facades\Http;
use Nikba\LaravelMaib\Auth\MaibAuthService;
use Nikba\LaravelMaib\Auth\TokenCache;
use Nikba\LaravelMaib\Exceptions\MaibApiException;
use Nikba\LaravelMaib\Tests\TestCase;

class MaibAuthServiceTest extends TestCase
{
    private MaibAuthService $auth;

    private TokenCache $cache;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cache = $this->app->make(TokenCache::class);
        $this->auth = $this->app->make(MaibAuthService::class);
    }

    public function test_token_generated_from_credentials(): void
    {
        Http::fake([
            'api.maibmerchants.md/v1/generate-token' => Http::response(
                $this->fakeTokenResponse('fresh-access-token')
            ),
        ]);

        $token = $this->auth->getValidToken();

        $this->assertSame('fresh-access-token', $token);
        Http::assertSentCount(1);
    }

    public function test_token_served_from_cache_without_http_call(): void
    {
        $this->cache->storeTokens([
            'accessToken' => 'cached-token',
            'refreshToken' => 'cached-refresh',
            'expiresIn' => 300,
            'refreshExpiresIn' => 1800,
        ]);

        Http::fake();

        $token = $this->auth->getValidToken();

        $this->assertSame('cached-token', $token);
        Http::assertNothingSent();
    }

    public function test_auto_refresh_when_access_token_expired(): void
    {
        // Store only a refresh token (simulating expired access token)
        $this->cache->storeTokens([
            'accessToken' => 'old-access',
            'refreshToken' => 'valid-refresh',
            'expiresIn' => -10,
            'refreshExpiresIn' => 1800,
        ]);

        // Manually expire the access token
        $this->cache->clearTokens();
        // Re-store only refresh token at cache level via storeTokens with a live refresh TTL
        $cacheStore = $this->app['cache']->store('array');
        $cacheStore->put('maib_token:refresh', 'valid-refresh', 1800);

        Http::fake([
            'api.maibmerchants.md/v1/generate-token' => Http::response(
                $this->fakeTokenResponse('refreshed-access-token')
            ),
        ]);

        $token = $this->auth->getValidToken();

        $this->assertSame('refreshed-access-token', $token);
        Http::assertSentCount(1);
    }

    public function test_full_reauth_when_both_tokens_expired(): void
    {
        $this->cache->clearTokens();

        Http::fake([
            'api.maibmerchants.md/v1/generate-token' => Http::response(
                $this->fakeTokenResponse('new-access-token')
            ),
        ]);

        $token = $this->auth->getValidToken();

        $this->assertSame('new-access-token', $token);
        Http::assertSentCount(1);
    }

    public function test_exception_thrown_on_invalid_credentials(): void
    {
        $this->cache->clearTokens();

        Http::fake([
            'api.maibmerchants.md/v1/generate-token' => Http::response(
                $this->fakeApiErrorResponse('11001', 'Invalid credentials.')
            ),
        ]);

        $this->expectException(MaibApiException::class);

        $this->auth->getValidToken();
    }

    public function test_fallback_to_credentials_on_refresh_error_11002(): void
    {
        $cacheStore = $this->app['cache']->store('array');
        $cacheStore->put('maib_token:refresh', 'bad-refresh', 1800);

        Http::fake([
            'api.maibmerchants.md/v1/generate-token' => Http::sequence()
                ->push($this->fakeApiErrorResponse('11002', 'Invalid refresh token.'))
                ->push($this->fakeTokenResponse('fallback-access-token')),
        ]);

        $token = $this->auth->getValidToken();

        $this->assertSame('fallback-access-token', $token);
        Http::assertSentCount(2);
    }
}
