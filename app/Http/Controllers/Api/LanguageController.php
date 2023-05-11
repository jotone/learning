<?php

namespace App\Http\Controllers\Api;

use App\Classes\FileHelper;
use App\Http\Controllers\BasicApiController;
use App\Http\Requests\Language\{LanguageStoreRequest, LanguageUpdateRequest};
use App\Traits\LanguageHelper;
use Illuminate\Http\JsonResponse;

class LanguageController extends BasicApiController
{
    use LanguageHelper;

    /**
     * Get language file content
     *
     * @param string $lang
     * @param string $file
     * @return JsonResponse
     */
    public function show(string $lang, string $file): JsonResponse
    {
        $path = lang_path($lang . '/' . $file . '.php');

        abort_if(!file_exists($path), 404);

        return response()->json([
            'list' => require $path,
            'origin' => require lang_path('en/' . $file . '.php')
        ]);
    }

    /**
     * Create language package
     *
     * @param LanguageStoreRequest $request
     * @return JsonResponse
     */
    public function store(LanguageStoreRequest $request): JsonResponse
    {
        // Get request data
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

        $this->writeTranslationsToFiles($source_data, $locale_data, $args['lang']);

        return response()->json($args['lang'], 201);
    }

    /**
     * Update language file content
     *
     * @param LanguageUpdateRequest $request
     * @return JsonResponse
     */
    public function update(LanguageUpdateRequest $request): JsonResponse
    {
        // Request data
        $args = $request->validated();
        // File path
        $path = lang_path($args['lang'] . '/' . $args['file'] . '.php');
        // Check if file exists
        abort_if(!file_exists($path), 404);

        $file_data = require $path;
        $file_data[$args['key']] = $args['value'];

        $this->writeTranslationsToFiles([$args['file'] => $file_data], [], $args['lang'], true);

        return response()->json([]);
    }

    /**
     * Remove language package folder
     *
     * @param string $name
     * @return JsonResponse
     */
    public function destroy(string $name): JsonResponse
    {
        // Package folder path
        $path = lang_path($name);
        // Remove package folder if exists
        if (is_dir($path)) FileHelper::recursiveRemove($path);

        return response()->json([], 204);
    }
}