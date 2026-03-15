<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Tests\Unit\Callback;

use Nikba\LaravelMaib\Callback\SignatureValidator;
use Nikba\LaravelMaib\Exceptions\MaibSignatureException;
use Nikba\LaravelMaib\Tests\TestCase;

class SignatureValidatorTest extends TestCase
{
    private string $signatureKey = 'test-signature-key';

    private function makeValidator(string $key = 'test-signature-key'): SignatureValidator
    {
        return new SignatureValidator($key);
    }

    private function computeSignature(array $result, string $key): string
    {
        ksort($result);
        $parts = array_values($result);
        $parts[] = $key;

        return base64_encode(hash('sha256', implode(':', $parts), true));
    }

    public function test_valid_signature_passes(): void
    {
        $result = [
            'payId' => 'abc-123',
            'status' => 'OK',
            'statusCode' => '000',
            'statusMessage' => 'Approved',
            'amount' => '99.99',
            'currency' => 'MDL',
            'orderId' => 'ORDER-001',
            'rrn' => '123456789012',
            'approval' => 'APP001',
            'cardNumber' => '411111****1111',
        ];

        $signature = $this->computeSignature($result, $this->signatureKey);
        $validator = $this->makeValidator();

        $this->assertTrue($validator->validate($result, $signature));
    }

    public function test_invalid_signature_throws_exception(): void
    {
        $result = ['payId' => 'abc-123', 'status' => 'OK'];
        $validator = $this->makeValidator();

        $this->expectException(MaibSignatureException::class);

        $validator->validate($result, 'bad-signature');
    }

    public function test_signature_with_different_key_fails(): void
    {
        $result = ['payId' => 'abc-123', 'status' => 'OK', 'statusCode' => '000', 'statusMessage' => 'OK', 'amount' => '10.00', 'currency' => 'MDL'];
        $signature = $this->computeSignature($result, 'wrong-key');
        $validator = $this->makeValidator('correct-key');

        $this->expectException(MaibSignatureException::class);

        $validator->validate($result, $signature);
    }

    public function test_recursive_sort_order_is_correct(): void
    {
        // Keys must be sorted alphabetically before hashing
        $result = [
            'zField' => 'last',
            'aField' => 'first',
            'mField' => 'middle',
        ];

        // Build expected signature with sorted keys
        $sorted = $result;
        ksort($sorted);
        $parts = array_values($sorted);
        $parts[] = $this->signatureKey;
        $expected = base64_encode(hash('sha256', implode(':', $parts), true));

        $validator = $this->makeValidator();
        $this->assertTrue($validator->validate($result, $expected));
    }
}
