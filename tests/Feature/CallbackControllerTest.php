<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Tests\Feature;

use Illuminate\Support\Facades\Event;
use Nikba\LaravelMaib\Events\MaibPaymentFailed;
use Nikba\LaravelMaib\Events\MaibPaymentReceived;
use Nikba\LaravelMaib\Tests\TestCase;

class CallbackControllerTest extends TestCase
{
    public function test_valid_signature_ok_status_returns_200_and_dispatches_received_event(): void
    {
        Event::fake();

        $payload = $this->fakeCallbackPayload(status: 'OK', signatureKey: 'test-signature-key');

        $response = $this->postJson('/maib/callback', $payload);

        $response->assertStatus(200);
        Event::assertDispatched(MaibPaymentReceived::class, function (MaibPaymentReceived $event) use ($payload) {
            return $event->payload->payId === $payload['result']['payId']
                && $event->payload->status->value === 'OK';
        });
        Event::assertNotDispatched(MaibPaymentFailed::class);
    }

    public function test_valid_signature_failed_status_returns_200_and_dispatches_failed_event(): void
    {
        Event::fake();

        $payload = $this->fakeCallbackPayload(status: 'FAILED', signatureKey: 'test-signature-key');

        $response = $this->postJson('/maib/callback', $payload);

        $response->assertStatus(200);
        Event::assertDispatched(MaibPaymentFailed::class, function (MaibPaymentFailed $event) {
            return $event->payload->status->value === 'FAILED';
        });
        Event::assertNotDispatched(MaibPaymentReceived::class);
    }

    public function test_invalid_signature_returns_422_and_dispatches_no_event(): void
    {
        Event::fake();

        $payload = $this->fakeCallbackPayload(status: 'OK', signatureKey: 'test-signature-key');
        $payload['signature'] = 'invalid-signature-value';

        $response = $this->postJson('/maib/callback', $payload);

        $response->assertStatus(422);
        Event::assertNotDispatched(MaibPaymentReceived::class);
        Event::assertNotDispatched(MaibPaymentFailed::class);
    }
}
