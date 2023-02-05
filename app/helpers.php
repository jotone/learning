<?php

use Illuminate\Support\Str;

/**
 * Generate url string
 *
 * @param string|null $str
 * @return string
 */
function generateUrl(?string $str): string
{
    return empty($str)
        ? ''
        : mb_strtolower(
            trim(
                preg_replace('/\-+/', '-', preg_replace('/[^a-zA-Z0-9_-]+/', '-', Str::ascii($str))),
                '-'
            )
        );
}

/**
 * Get validation rule translation
 * @param string $trans
 * @param string ...$arguments
 * @return string
 */
function lang(string $trans, string ...$arguments): string
{
    return preg_replace_array('/:[a-z]+/', $arguments, __($trans));
}