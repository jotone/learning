<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\SocialMedia as SocialMediaEnum;
use App\Http\Controllers\BaseDashboardController;
use App\Models\{Settings, SocialMedia};
use Inertia\Response;

class SettingsController extends BaseDashboardController
{
    /**
     * Returns the settings list page
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->view(
            view: 'Settings/Index',
            shared: [
                'breadcrumbs' => [
                    ['name' => 'Settings']
                ],
                'data' => Settings::select(['key', 'value'])
                    ->whereIn('key', [
                        'site_url',
                        'site_title',
                        'default_timezone',
                        'site_custom_url',
                        'zapier_key',
                        'digistore_key',
                        'enable_digistore'
                    ])
                    ->orWhereIn('section', ['email-settings', 'registration-process', 'smtp-settings', 'site-parts'])
                    ->get()
                    ->pluck('value', 'key')
                    ->toArray(),
                'socials' => [
                    'current' => SocialMedia::select(['id', 'caption', 'link'])->orderBy('position')->get()->toArray(),
                    'list' => SocialMediaEnum::forSelect(),
                ],
                'routes' => [
                    'settings' => [
                        'update' => route('api.settings.update')
                    ],
                    'socials' => [
                        'store' => route('api.socials.store')
                    ]
                ]
            ],
            scripts: [
                'css' => ['resources/assets/css/admin/settings.scss']
            ]);
    }
}