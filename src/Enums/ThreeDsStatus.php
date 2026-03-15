<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Enums;

enum ThreeDsStatus: string
{
    case AUTHENTICATED = 'AUTHENTICATED';
    case NOT_AUTHENTICATED = 'NOT_AUTHENTICATED';
    case UNAVAILABLE = 'UNAVAILABLE';
    case ATTEMPTED = 'ATTEMPTED';
    case REJECTED = 'REJECTED';
    case SKIPPED = 'SKIPPED';
    case NOTPARTICIPATED = 'NOTPARTICIPATED';
}
