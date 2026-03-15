<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Services;

use Nikba\LaravelMaib\Dto\Requests\ExecuteRecurringRequest;
use Nikba\LaravelMaib\Dto\Requests\SaveCardRecurringRequest;
use Nikba\LaravelMaib\Dto\Responses\ExecuteRecurringResponse;
use Nikba\LaravelMaib\Dto\Responses\InitPaymentResponse;
use Nikba\LaravelMaib\Http\MaibHttpClient;

class RecurringPaymentService
{
    public function __construct(
        private readonly MaibHttpClient $client,
    ) {}

    public function saveCard(SaveCardRecurringRequest $request): InitPaymentResponse
    {
        $data = $this->client->post('/savecard-recurring', $request->toArray());

        return InitPaymentResponse::fromArray($data);
    }

    public function execute(ExecuteRecurringRequest $request): ExecuteRecurringResponse
    {
        $data = $this->client->post('/execute-recurring', $request->toArray());

        return ExecuteRecurringResponse::fromArray($data);
    }
}
