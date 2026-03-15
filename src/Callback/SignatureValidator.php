<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Callback;

use Nikba\LaravelMaib\Exceptions\MaibSignatureException;

class SignatureValidator
{
    public function __construct(
        private readonly string $signatureKey,
    ) {}

    /**
     * Validates the HMAC-SHA256 signature from a callback payload.
     * Throws MaibSignatureException if invalid.
     */
    public function validate(array $result, string $signature): bool
    {
        $computed = $this->compute($result);

        if (! hash_equals($computed, $signature)) {
            throw new MaibSignatureException;
        }

        return true;
    }

    private function compute(array $result): string
    {
        $sorted = $this->sortByKeyRecursive($result);
        $sorted[] = $this->signatureKey;
        $string = $this->implodeRecursive(':', $sorted);

        return base64_encode(hash('sha256', $string, true));
    }

    private function sortByKeyRecursive(array $array): array
    {
        ksort($array);

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = $this->sortByKeyRecursive($value);
            }
        }

        return $array;
    }

    private function implodeRecursive(string $separator, array $array): string
    {
        $parts = [];

        foreach ($array as $value) {
            if (is_array($value)) {
                $parts[] = $this->implodeRecursive($separator, $value);
            } else {
                $parts[] = (string) $value;
            }
        }

        return implode($separator, $parts);
    }
}
