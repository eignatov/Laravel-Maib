<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Services;

use Nikba\LaravelMaib\Dto\Requests\ExecuteOneclickRequest;
use Nikba\LaravelMaib\Dto\Requests\SaveCardOneclickRequest;
use Nikba\LaravelMaib\Dto\Responses\InitPaymentResponse;
use Nikba\LaravelMaib\Http\MaibHttpClient;

class OneclickPaymentService
{
    public function __construct(
        private readonly MaibHttpClient $client,
    ) {}

    public function saveCard(SaveCardOneclickRequest $request): InitPaymentResponse
    {
        $data = $this->client->post('/savecard-oneclick', $request->toArray());

        return InitPaymentResponse::fromArray($data);
    }

    public function execute(ExecuteOneclickRequest $request): InitPaymentResponse
    {
        $data = $this->client->post('/execute-oneclick', $request->toArray());

        return InitPaymentResponse::fromArray($data);
    }
}
