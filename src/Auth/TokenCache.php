<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Auth;

use Illuminate\Contracts\Cache\Repository as CacheRepository;

class TokenCache
{
    private const KEY_ACCESS = 'access';

    private const KEY_REFRESH = 'refresh';

    private const TTL_BUFFER = 10;

    public function __construct(
        private readonly CacheRepository $cache,
        private readonly string $prefix = 'maib_token',
    ) {}

    public function getAccessToken(): ?string
    {
        return $this->cache->get($this->key(self::KEY_ACCESS));
    }

    public function getRefreshToken(): ?string
    {
        return $this->cache->get($this->key(self::KEY_REFRESH));
    }

    public function storeTokens(array $tokenData): void
    {
        $accessTtl = max(0, (int) ($tokenData['expiresIn'] ?? 300) - self::TTL_BUFFER);
        $refreshTtl = max(0, (int) ($tokenData['refreshExpiresIn'] ?? 1800) - self::TTL_BUFFER);

        $this->cache->put($this->key(self::KEY_ACCESS), $tokenData['accessToken'], $accessTtl);
        $this->cache->put($this->key(self::KEY_REFRESH), $tokenData['refreshToken'], $refreshTtl);
    }

    public function clearTokens(): void
    {
        $this->cache->forget($this->key(self::KEY_ACCESS));
        $this->cache->forget($this->key(self::KEY_REFRESH));
    }

    private function key(string $type): string
    {
        return "{$this->prefix}:{$type}";
    }
}
