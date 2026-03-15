<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Services;

use Nikba\LaravelMaib\Dto\Requests\DirectPaymentRequest;
use Nikba\LaravelMaib\Dto\Responses\InitPaymentResponse;
use Nikba\LaravelMaib\Http\MaibHttpClient;

class DirectPaymentService
{
    public function __construct(
        private readonly MaibHttpClient $client,
    ) {}

    public function initiate(DirectPaymentRequest $request): InitPaymentResponse
    {
        $data = $this->client->post('/pay', $request->toArray());

        return InitPaymentResponse::fromArray($data);
    }
}
