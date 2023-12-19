<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\BaseDashboardController;
use Inertia\Response;

class RoleController extends BaseDashboardController
{
    /**
     * Returns the index page of the Roles module.
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->view(
            view: 'Roles/List/Index',
            shared: [
                'routes' => [
                    'roles' => [
                        'api' => route('graphql.role'),
                        'create' => route('dashboard.roles.create'),
                        'edit' => route('dashboard.roles.edit', ':id')
                    ]
                ]
            ],
            scripts: [
                'css' => ['resources/assets/css/admin/content-table.scss']
            ]);
    }

    public function create()
    {

    }

    public function edit()
    {

    }
}