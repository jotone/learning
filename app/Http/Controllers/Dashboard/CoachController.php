<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\BaseDashboardController;
use App\Models\Role;
use App\Models\User;
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

    /**
     * Coach edit page
     *
     * @param User $coach
     * @return Response
     */
    public function edit(User $coach): Response
    {
        return $this->view('Coach/Form/Index', [
            'breadcrumbs' => [
                ['name' => 'Settings', 'url' => route('dashboard.settings.index')],
                ['name' => 'Edit Coach']
            ],
            'model' => $coach,
            'role_id' => Role::where('slug', 'coach')->value('id'),
            'routes' => [
                'users' => [
                    'api' => route('graphql.user')
                ]
            ]
        ]);
    }
}