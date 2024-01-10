<?php

namespace App\Enums;

use App\Traits\EnumTrait;

enum UserStatus: string
{
    use EnumTrait;

    case active = '0';
    case missing_details = '1';
    case inactive = '2';
    case suspended = '3';
}
