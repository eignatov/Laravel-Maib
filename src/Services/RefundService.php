<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Services;

use Nikba\LaravelMaib\Dto\Requests\RefundRequest;
use Nikba\LaravelMaib\Dto\Responses\RefundResponse;
use Nikba\LaravelMaib\Http\MaibHttpClient;

class RefundService
{
    public function __construct(
        private readonly MaibHttpClient $client,
    ) {}

    public function refund(RefundRequest $request): RefundResponse
    {
        $data = $this->client->post('/refund', $request->toArray());

        return RefundResponse::fromArray($data);
    }
}
