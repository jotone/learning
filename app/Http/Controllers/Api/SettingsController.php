<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use Illuminate\Http\{JsonResponse, Request};

class SettingsController extends Controller
{
    /**
     * Update settings
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $args = $request->only(['site_title', 'default_timezone', 'footer_code', 'header_code']);

        $result = [];
        foreach ($args as $key => $val) {
            if (in_array($key, ['site_title', 'default_timezone', 'footer_code', 'header_code'])) {
                $result[$key] = Settings::firstWhere('key', $key);
                $result[$key]->value = $val;
                $result[$key]->save();
            }
        }

        return response()->json($result);
    }
}