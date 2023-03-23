<?php

namespace App\Http\Controllers\Api;

use App\Classes\FileHelper;
use App\Http\Controllers\BasicApiController;
use App\Http\Requests\Language\LanguageStoreRequest;
use Illuminate\Http\JsonResponse;

class LanguageController extends BasicApiController
{
    /**
     * Create language package
     *
     * @param LanguageStoreRequest $request
     * @return JsonResponse
     */
    public function store(LanguageStoreRequest $request): JsonResponse
    {
        $args = $request->validated();

        // Get language translations file path
        $vendor_path = base_path('vendor/laravel-lang/lang/locales/' . $args['lang'] . '/php.json');
        // Check the translation file exists
        if (!file_exists($vendor_path)) {
            return response()->json([
                'errors' => [
                    'The language package "' . $args['lang'] . '" does not exist.'
                ]
            ], 404);
        }
        // Reinstall language package if it already exists
        if (file_exists(lang_path($args['lang']))) {
            FileHelper::recursiveRemove(lang_path($args['lang']));
        }
        // Source translation data
        $source_data = json_decode(file_get_contents(app_path('Console/Commands/InstallationData/lang_en.json')), 1);
        // Installable language package data
        $locale_data = json_decode(file_get_contents($vendor_path), 1);

        foreach ($source_data as $file_name => $content) {
            $result = '';
            foreach ($content as $row => $data) {
                if (is_array($data)) {
                    // Nested translations array
                    $result .= "    '$row' => [\n"; // Open array brackets
                    // Insert nested key => value pairs
                    foreach ($data as $key => $value) {
                        // Inner array translation value
                        $result .= $this->translationRow($locale_data, $value, "$row.$key", $key);
                    }
                    $result .= "    ],\n"; // Close array brackets
                } else {
                    // Translation key => value pair
                    $result .= $this->translationRow($locale_data, $data, $row);
                }
            }
            $folder = FileHelper::createFolder(lang_path($args['lang']));

            file_put_contents($folder . '/' . $file_name . '.php', "<?php\n\nreturn [\n$result];");
        }

        return response()->json($args['lang'], 201);
    }

    /**
     * Translation file row
     *
     * @param array $locale_data
     * @param string $data
     * @param string $row
     * @param string $key
     * @return string
     */
    protected function translationRow(array $locale_data, string $data, string $row, string $key = ''): string
    {
        $val = isset($locale_data[$row])
            ? preg_replace('/\'/', '&apos;', $this->lowercaseAttr($locale_data[$row]))
            : $data;
        $indent = empty($key) ? '    ' : '        ';
        return sprintf("%s'%s' => '%s',\n", $indent, empty($key) ? $row : $key, $val);
    }

    /**
     * Lowercase :attribute entry
     * @param string $str
     * @return string
     */
    protected function lowercaseAttr(string $str): string
    {
        return preg_replace_callback('/:[a-zA-Z]+/', fn($matches) => strtolower($matches[0]), $str);
    }
}