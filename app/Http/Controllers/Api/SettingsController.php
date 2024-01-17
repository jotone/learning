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
        $args = $request->only(['footer_code', 'header_code']);

        $result = [];
        foreach ($args as $key => $val) {
            if (in_array($key, ['footer_code', 'header_code'])) {
                $result[] = Settings::where('key', $key)->update([
                    'value' => $val
                ]);
            }
        }

        return response()->json($result);
    }
}