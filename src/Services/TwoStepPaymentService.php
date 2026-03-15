<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Services;

use Nikba\LaravelMaib\Dto\Requests\CapturePaymentRequest;
use Nikba\LaravelMaib\Dto\Requests\HoldPaymentRequest;
use Nikba\LaravelMaib\Dto\Responses\CaptureResponse;
use Nikba\LaravelMaib\Dto\Responses\InitPaymentResponse;
use Nikba\LaravelMaib\Http\MaibHttpClient;

class TwoStepPaymentService
{
    public function __construct(
        private readonly MaibHttpClient $client,
    ) {}

    public function hold(HoldPaymentRequest $request): InitPaymentResponse
    {
        $data = $this->client->post('/hold', $request->toArray());

        return InitPaymentResponse::fromArray($data);
    }

    public function capture(CapturePaymentRequest $request): CaptureResponse
    {
        $data = $this->client->post('/complete', $request->toArray());

        return CaptureResponse::fromArray($data);
    }
}
