<?php

namespace App\Http\Controllers\Api;

use App\Classes\FileHelper;
use App\Http\Controllers\BasicApiController;
use App\Http\Requests\Language\LanguageStoreRequest;
use App\Traits\LanguageHelper;
use Illuminate\Http\JsonResponse;
use Inertia\Response;

class LanguageController extends BasicApiController
{
    use LanguageHelper;

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

        $this->writeTranslationsToFiles($source_data, $locale_data, $args['lang']);

        return response()->json($args['lang'], 201);
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