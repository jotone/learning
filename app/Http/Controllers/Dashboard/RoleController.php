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
        return $this->view('Roles/Index', [
            'routes' => [
                'roles' => route('graphql.role')
            ]
        ]);
    }
}