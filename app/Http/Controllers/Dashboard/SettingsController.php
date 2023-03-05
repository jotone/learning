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
        return $this->view('Settings/Main', $request, [
            'routes' => [

            ],
            'content' => Settings::whereIn('section', ['site-info'])->get()->keyBy('key')
        ]);
    }
}