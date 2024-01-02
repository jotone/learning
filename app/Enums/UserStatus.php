<?php

namespace App\Enums;

enum UserStatus: string
{
    case 'active' = '0';
    case 'missing-details' = '1';
    case 'inactive' = '2';
    case 'suspended' = '3';
;}
