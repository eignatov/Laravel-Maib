<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Http;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Nikba\LaravelMaib\Auth\MaibAuthService;
use Nikba\LaravelMaib\Exceptions\MaibApiException;

class MaibHttpClient
{
    public function __construct(
        private readonly MaibAuthService $auth,
        private readonly string $baseUrl,
    ) {}

    /**
     * Standard JSON POST with Bearer token.
     */
    public function post(string $endpoint, array $data): array
    {
        $response = Http::withToken($this->auth->getValidToken())
            ->acceptJson()
            ->post($this->baseUrl.$endpoint, $data);

        if ($response->status() === 401) {
            $this->auth->invalidate();
            $response = Http::withToken($this->auth->getValidToken())
                ->acceptJson()
                ->post($this->baseUrl.$endpoint, $data);
        }

        return $this->handleResponse($response);
    }

    /**
     * GET request — no Content-Type header, no body.
     */
    public function get(string $endpoint): array
    {
        $response = Http::withToken($this->auth->getValidToken())
            ->accept('application/json')
            ->get($this->baseUrl.$endpoint);

        if ($response->status() === 401) {
            $this->auth->invalidate();
            $response = Http::withToken($this->auth->getValidToken())
                ->accept('application/json')
                ->get($this->baseUrl.$endpoint);
        }

        return $this->handleResponse($response);
    }

    /**
     * DELETE request — no Content-Type header, no body.
     */
    public function delete(string $endpoint): array
    {
        $response = Http::withToken($this->auth->getValidToken())
            ->accept('application/json')
            ->delete($this->baseUrl.$endpoint);

        if ($response->status() === 401) {
            $this->auth->invalidate();
            $response = Http::withToken($this->auth->getValidToken())
                ->accept('application/json')
                ->delete($this->baseUrl.$endpoint);
        }

        return $this->handleResponse($response);
    }

    private function handleResponse(Response $response): array
    {
        $body = $response->json();

        if (($body['ok'] ?? false) === false) {
            $error = $body['errors'][0] ?? $body['error'] ?? [];
            Log::error('maib API error', [
                'status' => $response->status(),
                'response' => $body,
            ]);
            throw MaibApiException::fromResponse($error);
        }

        return $body;
    }
}
