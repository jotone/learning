<?php

use Illuminate\Support\Str;

/**
 * Generate url string
 *
 * @param string $str
 * @return string
 */
function generateUrl(string $str): string
{
    return mb_strtolower(trim(preg_replace('/\-+/', '-', preg_replace('/[^a-zA-Z0-9_-]+/', '-', Str::ascii($str))), '-'));
}

/**
 * Recursive copy a directory or a file
 *
 * @param string $source
 * @param string $dest
 * @return void
 */
function recursiveCopy(string $source, string $dest): void
{
    if (is_dir($source)) {
        if (!file_exists($dest)) {
            mkdir($dest, 0755, true);
        }
        foreach (glob($source . '/*') as $node) {
            $name = pathinfo($node, PATHINFO_BASENAME);
            recursiveCopy($node, $dest . DIRECTORY_SEPARATOR . $name);
        }
    } else {
        copy($source, $dest);
    }
}

/**
 * Recursive remove a directory or a file
 *
 * @param string $source
 * @return void
 */
function recursiveDelete(string $source): void
{
    if (!is_dir($source)) {
        if (file_exists($source)) {
            unlink($source);
        }
    } else {
        foreach (glob($source . '/*') as $item) {
            recursiveDelete($item);
        }
        if (file_exists($source)) {
            rmdir($source);
        }
    }
}