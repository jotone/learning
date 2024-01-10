<?php

namespace App\Enums;

use App\Traits\EnumTrait;

enum ShirtSize: string
{
    use EnumTrait;

    case xs = '0';
    case s = '1';
    case m = '2';
    case l = '3';
    case xl = '4';
    case xxl = '5';
}
