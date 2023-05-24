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
                        'edit' => route('dashboard.users.roles.edit', ':id'),
                        'destroy' => route('api.roles.destroy', ':id')
                    ]
                ],
                'translations' => [
                    'role' => __('role')
                ]
            ]
        );
    }

    /**
     * Role create page
     *
     * @param Request $request
     * @return Response
     */
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

        return $this->form(
            template: 'Roles/Form',
            request: $request,
            share: [
                'permissions' => $permission_list,
                'routes' => [
                    'form' => route('api.roles.store')
                ],
                'translations' => [
                    'role' => [
                        'fields' => __('role.fields'),
                        'msg' => __('role.msg')
                    ]
                ],
                'userPermissions' => $this->userPermissions(Auth::user()->role->permissions)
            ]);
    }

    /**
     * Role edit page
     *
     * @param Role $role
     * @param Request $request
     * @return Response
     */
    public function edit(Role $role, Request $request): Response
    {
        try {
            $permission_list = $this->permissionList([
                app_path('Http/Controllers/Api/'),
                app_path('Http/Controllers/Dashboard/')
            ]);
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }

        $role->permission_list = $this->userPermissions($role->permissions);

        return $this->form(
            template: 'Roles/Form',
            request: $request,
            share: [
                'model' => $role,
                'permissions' => $permission_list,
                'routes' => [
                    'form' => route('api.roles.update', $role->id)
                ],
                'translations' => [
                    'role' => [
                        'fields' => __('role.fields'),
                        'msg' => __('role.msg')
                    ]
                ],
                'userPermissions' => $this->userPermissions(Auth::user()->role->permissions)
            ]);
    }
}