<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\BasicAdminController;
use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Response;

class RoleController extends BasicAdminController
{
    /**
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return $this->view('Roles/Index', $request, [
            'routes' => [
                'roles' => [
                    'list'    => route('api.roles.index'),
                    'edit'    => route('dashboard.users.roles.edit', 0),
                    'destroy' => route('api.roles.destroy', 0)
                ]
            ]
        ]);
    }

    public function create(Request $request): Response
    {
        return $this->view('Roles/Form', $request);
    }


    public function edit(Role $role, Request $request): Response
    {
        return $this->view('Roles/Form', $request);
    }
}