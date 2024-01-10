<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\BaseDashboardController;
use App\Models\Role;
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
                'breadcrumbs' => [
                    ['name' => 'Roles']
                ],
                'buttons' => [
                    [
                        'icon' => 'plus-icon',
                        'name' => 'Role Create',
                        'url' => route('dashboard.roles.create'),
                    ]
                ],
                'pageName' => 'Roles',
                'routes' => [
                    'roles' => [
                        'api' => route('graphql.role'),
                        'edit' => route('dashboard.roles.edit', ':id')
                    ]
                ]
            ],
            scripts: [
                'css' => ['resources/assets/css/admin/content-table.scss']
            ]);
    }

    /**
     * Returns the page for creating role
     *
     * @return Response
     */
    public function create(): Response
    {
        try {
            $permissions = [
                'graphql' => $this->graphQlList(),
                'dashboard' => $this->dashboardList()
            ];
        } catch (\ReflectionException $e) {
            abort(500, $e->getMessage());
        }

        return $this->view(
            view: 'Roles/Form/Index',
            shared: [
                'breadcrumbs' => [
                    ['name' => 'Roles', 'url' => route('dashboard.roles.index')],
                    ['name' => 'Create Role']
                ],
                'pageName' => 'Create Role',
                'permissions' => $permissions,
                'routes' => [
                    'roles' => [
                        'api' => route('graphql.role')
                    ]
                ]
            ],
            scripts: [
                'css' => ['resources/assets/css/admin/characteristics-table.scss']
            ]);
    }

    /**
     * Returns the page for editing role
     *
     * @param Role $role
     * @return Response
     */
    public function edit(Role $role): Response
    {
        try {
            $permissions = [
                'graphql' => $this->graphQlList(),
                'dashboard' => $this->dashboardList()
            ];
        } catch (\ReflectionException $e) {
            abort(500, $e->getMessage());
        }

        return $this->view(
            view: 'Roles/Form/Index',
            shared: [
                'breadcrumbs' => [
                    ['name' => 'Roles', 'url' => route('dashboard.roles.index')],
                    ['name' => 'Edit Role']
                ],
                'model' => $role->load('permissions'),
                'pageName' => sprintf('Edit Role "%s"', $role->name),
                'permissions' => $permissions,
                'routes' => [
                    'roles' => [
                        'api' => route('graphql.role')
                    ]
                ]
            ],
            scripts: [
                'css' => ['resources/assets/css/admin/characteristics-table.scss']
            ]);
    }

    /**
     * Generate list of permissions for GraphQL controllers
     * @return array
     */
    protected function graphQlList(): array
    {
        $permissions = [];
        foreach (glob(app_path('GraphQL/Schemas/') . '*.php') as $file) {
            $controller = pathinfo($file, PATHINFO_FILENAME);
            $class = 'App\GraphQL\Schemas\\' . $controller;
            $temp = (new $class())->toConfig();
            $methods = array_keys($temp['mutation']);
            if (!empty($temp['query'])) {
                $methods[] = 'query';
            }

            $permissions[$controller] = $methods;
        };

        return $permissions;
    }

    /**
     * Get list of permissions for Dashboard controllers
     * @return array
     * @throws \ReflectionException
     */
    protected function dashboardList(): array
    {
        $permissions = [];
        foreach (glob(app_path('Http/Controllers/Dashboard/') . '*.php') as $file) {
            $controller = pathinfo($file, PATHINFO_FILENAME);
            $class = 'App\Http\Controllers\Dashboard\\' . $controller;
            $methods = [];
            foreach ((new \ReflectionClass($class))->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                if ($method->class == $class && $method->name !== '__construct') {
                    $methods[] = $method->name;
                }
            }
            $permissions[$controller] = $methods;
        }

        return $permissions;
    }
}