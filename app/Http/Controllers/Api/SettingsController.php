<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\SmtpUpdateRequest;
use App\Mail\TestSMTP;
use App\Models\Settings;
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Facades\{Config, Mail};
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

class SettingsController extends Controller
{
    protected array $fields = [
        'common' => [
            'curriculum_menu',
            'custom_question_1',
            'custom_question_2',
            'custom_question_3',
            'default_timezone',
            'enable_digistore',
            'digistore_key',
            'enable_address',
            'enable_custom_question',
            'enable_lesson_complete',
            'enable_phone',
            'enable_search',
            'enable_shirt_size',
            'enable_signature',
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
     * Save smtp settings and send a test letter
     *
     * @param SmtpUpdateRequest $request
     * @return JsonResponse
     */
    public function smtp(SmtpUpdateRequest $request): JsonResponse
    {
        $args = $request->validated();

        Settings::where('key', 'smtp_username')->update(['value' => $args['smtp_username']]);
        Settings::where('key', 'smtp_password')->update(['value' => $args['smtp_password']]);
        Settings::where('key', 'smtp_host')->update(['value' => $args['smtp_host']]);
        Settings::where('key', 'smtp_port')->update(['value' => $args['smtp_port']]);
        Settings::where('key', 'smtp_encryption')->update(['value' => $args['smtp_encryption']]);
        Settings::where('key', 'smtp_from_address')->update(['value' => $args['smtp_from_address']]);
        Settings::where('key', 'smtp_from_name')->update(['value' => $args['smtp_from_name']]);

        if (config('app.env') != 'local') {
            Config::set('mail.mailers.smtp.username', $args['smtp_username']);
            Config::set('mail.mailers.smtp.password', $args['smtp_password']);
            Config::set('mail.mailers.smtp.host', $args['smtp_host']);
            Config::set('mail.mailers.smtp.port', $args['smtp_port']);
            Config::set('mail.mailers.smtp.encryption', $args['smtp_encryption']);
            Config::set('mail.from.address', $args['smtp_from_address']);
            Config::set('mail.from.name', $args['smtp_from_name']);
        }

        Mail::to(config('mail.from.address'))->send(new TestSMTP());

        return response()->json();
    }

    /**
     * Update settings
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $input = $request->only($this->flattenArray($this->fields));

        $result = [];
        foreach ($input as $key => $val) {
            if (in_array($key, $this->fields['common'])) {
                $result[$key] = Settings::firstWhere('key', $key);
                $result[$key]->value = $val;
                $result[$key]->save();
            }
        }

        return response()->json($result);
    }

    /**
     * Flatten a multidimensional array.
     *
     * @param array $array
     * @param array $result
     * @return array
     */
    protected function flattenArray(array $array, array $result = []): array
    {
        // Create a RecursiveIteratorIterator to iterate through the multidimensional array.
        $values = new RecursiveIteratorIterator(new RecursiveArrayIterator($array));
        // Append the value to the result array.
        foreach ($values as $val) {
            $result[] = $val;
        }
        // Return the flattened array.
        return $result;

    }
}
