<?php

namespace App\Enums;

use App\Traits\EnumTrait;

enum CategoryType: string
{
    use EnumTrait;

    case course = \App\Models\Course::class;
}
