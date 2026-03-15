<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Enums;

enum TransactionStatus: string
{
    case OK = 'OK';
    case FAILED = 'FAILED';
    case CREATED = 'CREATED';
    case PENDING = 'PENDING';
    case DECLINED = 'DECLINED';
    case TIMEOUT = 'TIMEOUT';
    case REVERSED = 'REVERSED';
}
