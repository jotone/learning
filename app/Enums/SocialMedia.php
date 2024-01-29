<?php

namespace App\Enums;

use App\Traits\EnumTrait;

enum SocialMedia: string
{
    use EnumTrait;

    case facebook = 'facebook-icon';
    case instagram = 'instagram-icon';
    case linkedin = 'linkedin-icon';
    case tiktok = 'tiktok-icon';
    case youtube = 'youtube-icon';
}
