<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Services;

use Nikba\LaravelMaib\Dto\Responses\DeleteCardResponse;
use Nikba\LaravelMaib\Http\MaibHttpClient;

class DeleteCardService
{
    public function __construct(
        private readonly MaibHttpClient $client,
    ) {}

    /**
     * DELETE /delete-card/{billerId} — no Content-Type header, no body.
     */
    public function delete(string $billerId): DeleteCardResponse
    {
        $data = $this->client->delete('/delete-card/'.$billerId);

        return DeleteCardResponse::fromArray($data);
    }
}
