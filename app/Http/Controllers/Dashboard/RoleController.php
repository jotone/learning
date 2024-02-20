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
                'routes' => [
                    'api' => route('graphql.role'),
                    'create' => route('dashboard.roles.create'),
                    'edit' => route('dashboard.roles.edit', ':id')
                ]
            ],
            scripts: [
                'css' => ['resources/assets/css/admin/content-table.scss']
            ]);
    }

    /**
     * Returns the page for creating a role
     *
     * @return Response
     */
    public function create(): Response
    {
        try {
            $permissions = [
                'api' => $this->controllerList('Http/Controllers/Api/'),
                'graphql' => $this->graphQlList(),
                'dashboard' => $this->controllerList('Http/Controllers/Dashboard/')
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
     * Returns the page for an editing role
     *
     * @param Role $role
     * @return Response
     */
    public function edit(Role $role): Response
    {
        try {
            $permissions = [
                'api' => $this->controllerList('Http/Controllers/Api/'),
                'graphql' => $this->graphQlList(),
                'dashboard' => $this->controllerList('Http/Controllers/Dashboard/')
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
     * Generate a list of permissions for GraphQL controllers
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
     * Get a list of permissions for Dashboard controllers
     * @param string $folder
     * @return array
     * @throws \ReflectionException
     */
    protected function controllerList(string $folder): array
    {
        $permissions = [];
        foreach (glob(app_path($folder) . '*.php') as $file) {
            $controller = pathinfo($file, PATHINFO_FILENAME);
            $class = 'App\\' . preg_replace('/\//', '\\', $folder) . $controller;
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