<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Dto\Responses;

readonly class DeleteCardResponse
{
    public function __construct(
        public string $billerId,
        public string $status,
    ) {}

    public static function fromArray(array $data): self
    {
        $result = $data['result'] ?? $data;

        return new self(
            billerId: $result['billerId'],
            status: $result['status'],
        );
    }
}
