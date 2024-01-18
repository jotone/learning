<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\BaseDashboardController;
use App\Models\Settings;
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
                'data' => Settings::select(['key', 'value', 'extra_data'])->whereIn('key', [
                    'site_url',
                    'site_title',
                    'default_timezone',
                    'site_custom_url'
                ])->get()->keyBy('key')->toArray(),
                'routes' => [
                    'settings' => [
                        'update' => route('api.settings.update')
                    ]
                ]
            ],
            scripts: [
                'css' => ['resources/assets/css/admin/settings.scss']
            ]);
    }
}