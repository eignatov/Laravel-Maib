<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib;

use Illuminate\Contracts\Foundation\Application;
use Nikba\LaravelMaib\Dto\Requests\CapturePaymentRequest;
use Nikba\LaravelMaib\Dto\Requests\DirectPaymentRequest;
use Nikba\LaravelMaib\Dto\Requests\ExecuteOneclickRequest;
use Nikba\LaravelMaib\Dto\Requests\ExecuteRecurringRequest;
use Nikba\LaravelMaib\Dto\Requests\HoldPaymentRequest;
use Nikba\LaravelMaib\Dto\Requests\RefundRequest;
use Nikba\LaravelMaib\Dto\Requests\SaveCardOneclickRequest;
use Nikba\LaravelMaib\Dto\Requests\SaveCardRecurringRequest;
use Nikba\LaravelMaib\Dto\Responses\CaptureResponse;
use Nikba\LaravelMaib\Dto\Responses\DeleteCardResponse;
use Nikba\LaravelMaib\Dto\Responses\ExecuteRecurringResponse;
use Nikba\LaravelMaib\Dto\Responses\InitPaymentResponse;
use Nikba\LaravelMaib\Dto\Responses\PayInfoResponse;
use Nikba\LaravelMaib\Dto\Responses\RefundResponse;
use Nikba\LaravelMaib\Services\DeleteCardService;
use Nikba\LaravelMaib\Services\DirectPaymentService;
use Nikba\LaravelMaib\Services\OneclickPaymentService;
use Nikba\LaravelMaib\Services\PayInfoService;
use Nikba\LaravelMaib\Services\RecurringPaymentService;
use Nikba\LaravelMaib\Services\RefundService;
use Nikba\LaravelMaib\Services\TwoStepPaymentService;

class MaibManager
{
    public function __construct(
        private readonly Application $app,
    ) {}

    public function directPayment(DirectPaymentRequest $request): InitPaymentResponse
    {
        return $this->app->make(DirectPaymentService::class)->initiate($request);
    }

    public function hold(HoldPaymentRequest $request): InitPaymentResponse
    {
        return $this->app->make(TwoStepPaymentService::class)->hold($request);
    }

    public function capture(CapturePaymentRequest $request): CaptureResponse
    {
        return $this->app->make(TwoStepPaymentService::class)->capture($request);
    }

    public function refund(RefundRequest $request): RefundResponse
    {
        return $this->app->make(RefundService::class)->refund($request);
    }

    public function payInfo(string $payId): PayInfoResponse
    {
        return $this->app->make(PayInfoService::class)->get($payId);
    }

    public function savecardRecurring(SaveCardRecurringRequest $request): InitPaymentResponse
    {
        return $this->app->make(RecurringPaymentService::class)->saveCard($request);
    }

    public function executeRecurring(ExecuteRecurringRequest $request): ExecuteRecurringResponse
    {
        return $this->app->make(RecurringPaymentService::class)->execute($request);
    }

    public function savecardOneclick(SaveCardOneclickRequest $request): InitPaymentResponse
    {
        return $this->app->make(OneclickPaymentService::class)->saveCard($request);
    }

    public function executeOneclick(ExecuteOneclickRequest $request): InitPaymentResponse
    {
        return $this->app->make(OneclickPaymentService::class)->execute($request);
    }

    public function deleteCard(string $billerId): DeleteCardResponse
    {
        return $this->app->make(DeleteCardService::class)->delete($billerId);
    }
}
