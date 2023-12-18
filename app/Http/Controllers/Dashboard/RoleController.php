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
            view: 'Roles/Index',
            shared: [
                'routes' => [
                    'roles' => route('graphql.role')
                ]
            ],
            scripts: [
                'css' => ['resources/assets/css/admin/content-table.scss']
            ]);
    }
}