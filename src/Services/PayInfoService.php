<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Services;

use Nikba\LaravelMaib\Dto\Responses\PayInfoResponse;
use Nikba\LaravelMaib\Http\MaibHttpClient;

class PayInfoService
{
    public function __construct(
        private readonly MaibHttpClient $client,
    ) {}

    /**
     * GET /pay-info/{payId} — no Content-Type header, no body.
     */
    public function get(string $payId): PayInfoResponse
    {
        $data = $this->client->get('/pay-info/'.$payId);

        return PayInfoResponse::fromArray($data);
    }
}
