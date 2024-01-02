<?php

namespace App\Enums;

enum CourseTracking: string
{
    case 'enable_auto_approve' = '0';
    case 'enable_for_every_submission' = '1';
    case 'enable_for_first_submission' = '2';
}
