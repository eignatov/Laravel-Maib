<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib\Enums;

enum Currency: string
{
    case MDL = 'MDL';
    case EUR = 'EUR';
    case USD = 'USD';
}
