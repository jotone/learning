<?php

namespace App\Http\Controllers;

use App\Models\{AdminMenu, Settings};
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Inertia\{Inertia, Response};

class BaseDashboardController extends Controller
{
    /**
     * Render the specified view
     *
     * @param string $view
     * @return Response
     */
    protected function view(string $view): Response
    {
        return Inertia::render($view, [
            'menu' => $this->buildSideMenu(),
            'routes' => [
                'dashboard' => [
                    'index' => route('dashboard.index'),
                ],
                'auth' => [
                    'logout' => route('auth.logout')
                ]
            ],
            'settings' => Settings::whereIn('key', [
                'fav_icon',
                'logo_img_admin',
                'logo_img_mobile',
                'main_language',
                'package_type',
                'site_title'
            ])->pluck('value', 'key')->toArray()
        ]);
    }

    /**
     * Build side menu list
     *
     * @return array
     */
    protected function buildSideMenu(): array
    {
        $user_permissions = $this->getAuthUserPermissions();
        $routes = $this->getDashboardRoutes();
        $side_menu = AdminMenu::select(['name', 'route', 'img', 'section'])
            ->whereNull('parent_id')
            ->orderBy('section')
            ->orderBy('position')
            ->get()
            ->map(
                fn($model) => str_starts_with($model->route, 'http') || (
                    isset($routes[$model->route])
                    && isset($user_permissions[$routes[$model->route]])
                )
                    ? $model
                    : null
            );
        $menu = [];
        foreach ($side_menu->filter() as $item) {
            $menu[$item->section][] = [
                'name' => $item->name,
                'img' => $item->img,
                'route' => $item->route
            ];
        }

        return $menu;
    }

    /**
     * Get user permissions list as Controller @ action array
     *
     * @return array
     */
    protected function getAuthUserPermissions(): array
    {
        $result = [];
        foreach (auth()->user()->role->permissions as $permission) {
            foreach ($permission->allowed_methods as $method) {
                $result[] = $permission->controller . '@' . $method;
            }
        }

        return array_flip($result);
    }

    /**
     * Retrieve list of dashboard menu methods with actions
     *
     * @return array
     */
    protected function getDashboardRoutes(): array
    {
        $result = [];
        foreach (Route::getRoutes()->getRoutes() as $route) {
            if (
                str_starts_with($route->uri, 'dashboard')
                && !str_contains($route->uri, '/create')
                && !str_contains($route->uri, '/edit')
                && is_string($route->action['uses'])
            ) {
                $result[Str::start($route->uri, '/')] = $route->action['uses'];
            }
        }

        return $result;
    }
}