<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use Illuminate\Http\{JsonResponse, Request};

class SettingsController extends Controller
{
    protected array $fields = [
        'common' => [
            'curriculum_menu',
            'custom_question_1',
            'custom_question_2',
            'custom_question_3',
            'default_timezone',
            'digistore_enable',
            'digistore_key',
            'enable_address',
            'enable_custom_question',
            'enable_help_center',
            'enable_lesson_complete',
            'enable_phone',
            'enable_search',
            'enable_shirt_size',
            'enable_signature',
            'enable_sticky_menu',
            'footer_code',
            'header_code',
            'search_title',
            'site_title',
            'zapier_key'
        ],
        'custom_url' => [
            'site_custom_url',
        ]
    ];

    /**
     * Update settings
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $args = $request->only(flattenArray($this->fields));

        $result = [];
        foreach ($args as $key => $val) {
            if (in_array($key, $this->fields['common'])) {
                $result[$key] = Settings::firstWhere('key', $key);
                $result[$key]->value = $val;
                $result[$key]->save();
            }
        }

        return response()->json($result);
    }
}