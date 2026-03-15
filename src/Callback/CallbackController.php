<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Callback;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Nikba\LaravelMaib\Dto\Responses\CallbackPayload;
use Nikba\LaravelMaib\Enums\TransactionStatus;
use Nikba\LaravelMaib\Events\MaibPaymentFailed;
use Nikba\LaravelMaib\Events\MaibPaymentReceived;
use Nikba\LaravelMaib\Exceptions\MaibSignatureException;

class CallbackController extends Controller
{
    public function __construct(
        private readonly SignatureValidator $validator,
    ) {}

    public function handle(Request $request): Response
    {
        $data = $request->json()->all();

        $result = $data['result'] ?? [];
        $signature = $data['signature'] ?? '';

        try {
            $this->validator->validate($result, $signature);
        } catch (MaibSignatureException $e) {
            Log::warning('maib callback invalid signature', ['ip' => $request->ip()]);

            return response('Signature mismatch.', 422);
        }

        $payload = CallbackPayload::fromArray($data);

        if ($payload->status === TransactionStatus::OK) {
            Event::dispatch(new MaibPaymentReceived($payload));
        } else {
            Event::dispatch(new MaibPaymentFailed($payload));
        }

        return response('OK', 200);
    }
}
