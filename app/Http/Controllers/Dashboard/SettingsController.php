<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\SocialMedia as SocialMediaEnum;
use App\Http\Controllers\BaseDashboardController;
use App\Models\{EmailTemplate, Role, Settings, SocialMedia};
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
                'roles' => Role::whereIn('slug', ['coach'])->pluck('id')->toArray(),
                'routes' => [
                    'settings' => [
                        'smtp' => route('api.settings.smtp'),
                        'update' => route('api.settings.update')
                    ],
                    'socials' => [
                        'destroy' => route('api.socials.destroy', ':id'),
                        'sort' => route('api.socials.sort'),
                        'store' => route('api.socials.store'),
                        'update' => route('api.socials.update', ':id')
                    ],
                    'templates' => [
                        'create' => route('dashboard.settings.templates.create'),
                        'destroy' => route('api.templates.destroy', ':id'),
                        'edit' => route('dashboard.settings.templates.edit', ':id')
                    ],
                    'user' => [
                        'edit' => '#:id',
                        'api' => route('graphql.user')
                    ]
                ],
                'socials' => [
                    'current' => SocialMedia::select(['id', 'type', 'caption', 'link', 'icon'])->orderBy('position')->get()->toArray(),
                    'list' => SocialMediaEnum::forSelect(),
                ],
                'templates' => EmailTemplate::select(['id', 'title'])->get()
            ],
            scripts: [
                'css' => [
                    'resources/assets/css/admin/content-table.scss',
                    'resources/assets/css/admin/settings.scss'
                ]
            ]);
    }
}