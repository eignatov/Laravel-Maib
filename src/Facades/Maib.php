<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Facades;

use Illuminate\Support\Facades\Facade;
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
use Nikba\LaravelMaib\MaibManager;

/**
 * @method static InitPaymentResponse directPayment(DirectPaymentRequest $request)
 * @method static InitPaymentResponse hold(HoldPaymentRequest $request)
 * @method static CaptureResponse capture(CapturePaymentRequest $request)
 * @method static RefundResponse refund(RefundRequest $request)
 * @method static PayInfoResponse payInfo(string $payId)
 * @method static InitPaymentResponse savecardRecurring(SaveCardRecurringRequest $request)
 * @method static ExecuteRecurringResponse executeRecurring(ExecuteRecurringRequest $request)
 * @method static InitPaymentResponse savecardOneclick(SaveCardOneclickRequest $request)
 * @method static InitPaymentResponse executeOneclick(ExecuteOneclickRequest $request)
 * @method static DeleteCardResponse deleteCard(string $billerId)
 *
 * @see MaibManager
 */
class Maib extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'maib';
    }
}
