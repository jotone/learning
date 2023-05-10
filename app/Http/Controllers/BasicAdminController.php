<?php

namespace App\Http\Controllers;

use App\Models\{AdminMenu, Settings};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Inertia\{Inertia, Response};

class BasicAdminController extends Controller
{
    protected array $order = [
        'by' => 'created_at',
        'dir' => 'desc'
    ];

    /**
     * Default list-page render. Adds to rendered vue component default filters variable
     *
     * @param string $template
     * @param Request $request
     * @param array $share
     * @param bool $use_filters
     * @return Response
     */
    protected function view(string $template, Request $request, array $share = [], bool $use_filters = true): Response
    {
        return $this->form(
            template: $template,
            request: $request,
            share: array_merge_recursive([
                'filters' => [
                    'order' => [
                        'by' => $this->order['by'],
                        'dir' => $this->order['dir']
                    ],
                    'page' => $request->get('page', 1),
                    'search' => $request->get('search', ''),
                    'take' => $this->take
                ]
            ], $share)
        );
    }

    /**
     * Default page render.
     *   Adds to rendered vue component default variables: routes, admin side menu,
     *   top menu, then merges them with shared
     *
     * @param string $template
     * @param Request $request
     * @param array $share
     * @return Response
     */
    protected function form(string $template, Request $request, array $share = []): Response
    {
        // Get trimmed url
        $path = rtrim(preg_replace('/(\/create|\/me|\/edit\/\d*)/', '', $request->getPathInfo()), '/');
        // Build parent item for the current route to build top menu
        $parent_item = $this->getMenuParent(AdminMenu::firstWhere(['route' => $path]));
        // Get user permissions list
        $user_permissions = $this->getAuthUserPermissions();
        // Get available menu route list
        $routes = $this->getDashboardRoutes();
        // Build side menu list
        $side_menu = AdminMenu::select(['name', 'route', 'img', 'is_top'])
            ->whereNull('parent_id')
            ->orderBy('position')
            ->get()
            ->map(function ($model) use ($user_permissions, $routes) {
                if (
                    (isset($routes[$model->route]) && isset($user_permissions[$routes[$model->route]]))
                    || str_starts_with($model->route, 'http')
                ) {
                    // Set image to menu item
                    $image = public_path('images/icons/' . $model->img . '.svg');
                    $model->img = file_exists($image) ? file_get_contents($image) : null;
                    return $model;
                }
                return null;
            });

        return Inertia::render($template, array_merge_recursive(
            [
                // Side menu
                'menu' => $side_menu->filter(),
                // Default routes
                'routes' => [
                    'auth' => [
                        'me' => route('dashboard.users.me'),
                        'logout' => route('auth.logout')
                    ],
                    'dashboard' => [
                        'index' => route('dashboard.index', [], false)
                    ]
                ],
                // Default site settings
                'settings' => Settings::where('section', 'hidden')->pluck('value', 'key')->toArray(),
                // Top menu
                'topMenu' => AdminMenu::select(['name', 'route'])
                    ->where('parent_id', $parent_item->id)
                    ->orderBy('position')
                    ->get()
            ],
            $share
        ));
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
     * Get topper menu element
     *
     * @param AdminMenu $item
     * @return AdminMenu
     */
    protected function getMenuParent(AdminMenu $item): AdminMenu
    {
        return $item->parent_id ? $this->getMenuParent($item->parent) : $item;
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