<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\BasicAdminController;
use App\Models\Settings;
use Illuminate\Http\Request;
use Inertia\Response;

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
        return $this->view(
            template: 'Settings/Main',
            request: $request,
            share: [
                'routes'  => [
                    'settings' => [
                        'update' => route('api.settings.update')
                    ]
                ],
                'content' => Settings::whereIn('section', ['custom-scripts', 'site-info', 'main-colors'])
                    ->get()
                    ->keyBy('key'),
            ],
            prevent_filters: true
        );
    }
}