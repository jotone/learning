<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\BaseDashboardController;
use App\Models\Role;
use Inertia\Response;

class CoachController extends BaseDashboardController
{
    /**
     * Coach creation page
     *
     * @return Response
     */
    public function create(): Response
    {
        return $this->view('Coach/Form/Index', [
            'breadcrumbs' => [
                ['name' => 'Settings', 'url' => route('dashboard.settings.index')],
                ['name' => 'Create Coach']
            ],
            'role_id' => Role::where('slug', 'coach')->value('id'),
            'routes' => [
                'users' => [
                    'api' => route('graphql.user')
                ]
            ]
        ]);
    }

    public function edit()
    {

    }
}