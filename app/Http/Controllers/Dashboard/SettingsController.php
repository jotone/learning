<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\BasicAdminController;
use App\Models\EmailTemplate;
use App\Models\Settings;
use App\Models\SocialMediaLink;
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
                'routes'      => [
                    'settings' => [
                        'update' => route('api.settings.update')
                    ]
                ],
                'content'     => Settings::whereIn('section', [
                    'custom-scripts',
                    'site-info',
                    'main-colors'
                ])->get()->keyBy('key'),
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
                'routes'  => [
                    'settings' => [
                        'update' => route('api.settings.update')
                    ]
                ],
                'content' => Settings::where('section', 'login-page')->get()->keyBy('key'),
            ]
        );
    }

    /**
     * Email Settings page
     *
     * @param Request $request
     * @return Response
     */
    public function email(Request $request): Response
    {
        return $this->form(
            template: 'Settings/Email',
            request: $request,
            share: [
                'routes'    => [
                    'emails'   => [
                        'create'  => route('dashboard.settings.emails.create'),
                        'edit'    => route('dashboard.settings.emails.edit', 0),
                        'destroy' => route('api.email-templates.destroy', 0)
                    ],
                    'settings' => [
                        'update' => route('api.settings.update')
                    ],
                    'social'   => [
                        'store'   => route('api.socials.store'),
                        'update'  => route('api.socials.update', 0),
                        'sort'    => route('api.socials.sort'),
                        'destroy' => route('api.socials.destroy', 0)
                    ]
                ],
                'content'   => Settings::whereIn('section', ['smtp-settings', 'email-settings'])->get()->keyBy('key'),
                'social'    => SocialMediaLink::orderBy('position')->get(),
                'templates' => EmailTemplate::select('id', 'title')->orderBy('created_at', 'desc')->get()
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
                'routes'    => [
                    'language' => [
                        'destroy' => route('api.language.destroy', 0),
                        'store'   => route('api.language.store'),
                    ],
                    'settings' => [
                        'update' => route('api.settings.update')
                    ]
                ]
            ]
        );
    }
}