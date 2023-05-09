<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\BasicAdminController;
use App\Models\Role;
use App\Traits\PermissionListTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

class RoleController extends BasicAdminController
{
    use PermissionListTrait;

    /**
     * Role list page
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return $this->view(
            template: 'Roles/Index',
            request: $request,
            share: [
                'routes' => [
                    'roles' => [
                        'list' => route('api.roles.index'),
                        'create' => route('dashboard.users.roles.create'),
                        'edit' => route('dashboard.users.roles.edit', 0),
                        'destroy' => route('api.roles.destroy', 0)
                    ]
                ]
            ]
        );
    }

    public function create(Request $request): Response
    {
        try {
            $permission_list = $this->permissionList([
                app_path('Http/Controllers/Api/'),
                app_path('Http/Controllers/Dashboard/')
            ]);
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }

        return $this->view('Roles/Form', $request, [
            'routes' => [
                'roles' => [
                    'form' => route('api.roles.store')
                ]
            ],
            'permissions' => $permission_list,
            'user_permissions' => $this->userPermissions()
        ]);
    }


    public function edit(Role $role, Request $request): Response
    {
        return $this->view('Roles/Form', $request);
    }

    /**
     * Build user permission list
     * @param array $result
     * @return array
     */
    protected function userPermissions(array $result = []): array
    {
        foreach (Auth::user()->role->permissions as $item) {
            $result[$item->controller] = $item->allowed_methods;
        }

        return $result;
    }
}