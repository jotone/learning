<?php

namespace App\Classes;

use Illuminate\Support\Str as StringOperations;

class Str extends StringOperations
{

    /**
     * Generates a URL-friendly string from the given input.
     *
     * @param String|null $str
     * @return string
     */
    public static function generateUrl(?string $str): string
    {
        return empty($str)
            ? ''
            : mb_strtolower(
                trim(
                    preg_replace('/\-+/', '-', preg_replace(
                        '/[^a-zA-Z0-9_-]+/', '-',
                        self::transliterate($str))),
                    '-'
                )
            );
    }
}