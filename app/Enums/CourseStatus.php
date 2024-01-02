<?php

namespace App\Enums;

enum CourseStatus: string
{
    case 'active' = '0';
    case 'coming_soon' = '1';
    case 'draft' = '2';
}