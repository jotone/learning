<?php

use App\Models\Settings;
use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * Flatten a multidimensional array.
 *
 * @param array $array
 * @param array $result
 * @return array
 */
function flattenArray(array $array, array $result = []): array
{
    // Create a RecursiveIteratorIterator to iterate through the multidimensional array.
    $values = new RecursiveIteratorIterator(new RecursiveArrayIterator($array));
    // Append the value to the result array.
    foreach($values as $val) {
        $result[] = $val;
    }
    // Return the flattened array.
    return $result;
}

/**
 * Generates a URL-friendly string from the given input.
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
 * Retrieves the subdomain from a given URL.
 *
 * @param string $url
 * @return string|null
 */
function getSubDomain(string $url): ?string
{
    $urlParts = parse_url($url);

    if (isset($urlParts['host'])) {
        $hostParts = explode('.', $urlParts['host']);

        // Check if there are more than two parts (subdomain exists)
        if (count($hostParts) > 2) {
            return $hostParts[0]; // The first part is the subdomain
        }
    }

    return null; // No subdomain found
}

/**
 * Checks if the given variable is a valid JSON string.
 *
 * @param mixed $var
 * @return bool
 */
function isJson(mixed $var): bool
{
    json_decode($var);
    return json_last_error() === JSON_ERROR_NONE;
}

/**
 * Converts a value to a boolean representation.
 *
 * @param mixed $value
 * @return bool
 */
function toBool($value): bool
{
    return in_array($value === 0 ? false : $value, [1, '1', true, 'true', 'on']);
}

/**
 * Converts the given date to a Carbon instance.
 *
 * @param mixed $date The date to convert.
 * @param bool $strict Determines whether the conversion should be strict or not. Default is true.
 * @return Carbon|null A Carbon instance representing the converted date or null if the conversion fails.
 * @throws \Exception If the date cannot be parsed and strict mode is enabled.
 */
function toCarbon(mixed $date, bool $strict = true): ?Carbon
{
    if ($date instanceof Carbon) {
        return $date;
    }

    $time_formats = array_merge(
        [Settings::where('key', 'time_format')->value('value')],
        ['Y-m-d', 'Y/m/d', 'j/M/Y', 'j/m/Y', 'j n, Y', 'j M, Y', 'Y, m j', 'Y, M j', 'm.d.Y', 'm/d/Y']
    );

    $formats = [];
    foreach ($time_formats as $format) {
        $formats[] = $format . ' H:i:s';
        $formats[] = $format . ' H:i';
        $formats[] = $format;
    }

    foreach ($formats as $format) {
        if (Carbon::hasFormat($date, $format)) {
            return Carbon::createFromFormat($format, $date);
        }
    }

    if ($strict) {
        throw new Exception('Cannot parse date ' . $date);
    } else {
        return null;
    }
}

/**
 * Generates a HMAC string using the CopeCart ID, admin server access key, and current time.
 *
 * @return string
 */
function generateHMAC(): string
{
    $keys = Settings::whereIn('key', ['copecart_id', 'admin_server_access_key'])->pluck('value', 'key')->toArray();

    return hash_hmac('sha256', $keys['copecart_id'] . $keys['admin_server_access_key'], md5(date('H:i')));
}