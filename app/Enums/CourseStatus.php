<?php

namespace App\Enums;

use App\Traits\EnumTrait;

enum CourseStatus: string
{
    use EnumTrait;

    case active = '0';
    case coming_soon = '1';
    case draft = '2';
}