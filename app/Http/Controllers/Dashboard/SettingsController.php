<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\BasicAdminController;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Response;
use LaravelLang\Publisher\Constants\Locales;
use LaravelLang\Publisher\Facades\Helpers\Locales as LocaleHelper;

class SettingsController extends BasicAdminController
{
    /**
     * Main settings page
     *
     * @param Request $request
     * @return Response
     */
    public function main(Request $request): Response
    {
        $override_path = public_path('css/override.css');
        return $this->form(
            template: 'Settings/Main',
            request: $request,
            share: [
                'routes' => [
                    'settings' => [
                        'update' => route('api.settings.update')
                    ]
                ],
                'content' => Settings::whereIn('section', ['custom-scripts', 'site-info', 'main-colors'])
                    ->get()
                    ->keyBy('key'),
                'overrideCss' => file_exists($override_path) ? file_get_contents($override_path) : ''
            ]
        );
    }

    /**
     * Login page settings
     *
     * @param Request $request
     * @return Response
     */
    public function loginPage(Request $request): Response
    {
        return $this->form(
            template: 'Settings/Login',
            request: $request,
            share: [
                'routes' => [
                    'settings' => [
                        'update' => route('api.settings.update')
                    ]
                ],
                'content' => Settings::whereIn('section', ['login-page', 'login-page'])
                    ->orWhere('key', 'logo_img')
                    ->get()
                    ->keyBy('key'),
            ]
        );
    }

    /**
     * Language settings page
     *
     * @param Request $request
     * @return Response
     */
    public function language(Request $request): Response
    {
        return $this->form(
            template: 'Settings/Language',
            request: $request,
            share: [
                'installed' => LocaleHelper::installed(),
                'available' => array_map(
                    fn($str) => 'CHINESE_T' === $str ? 'Chinese Taiwan'
                        : Str::headline(Str::lower(preg_replace('/_/', ' ', $str))),
                    collect(Locales::cases())->pluck('name', 'value')->toArray()
                ),
                'routes' => [
                    'language' => [
                        'show' => route('api.language.show', [':lang', ':file']),
                        'store' => route('api.language.store'),
                        'update' => route('api.language.update'),
                        'destroy' => route('api.language.destroy', ':lang')
                    ],
                    'settings' => [
                        'update' => route('api.settings.update')
                    ]
                ],
                'files' => array_map(fn($file) => pathinfo($file, PATHINFO_FILENAME), glob(lang_path('en') . '/*.php'))
            ]
        );
    }
}