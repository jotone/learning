<?php

namespace App\Http\Controllers;

use App\Models\{AdminMenu, PageColumnSection, Settings};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Inertia\{Inertia, Response};

class BaseDashboardController extends Controller
{
    /**
     * Render the specified content-table view
     *
     * @param string $view
     * @param string $section
     * @param array $shared
     * @param array $scripts
     * @return Response
     */
    protected function contentTable(string $view, string $section, array $shared = [], array $scripts = []): Response
    {
        // Get page column sections
        if (!empty($section)) {
            $shared['routes']['page_columns'] = [
                'update' => route('api.page-columns.update', ':id'),
                'sort' => route('api.page-columns.sort')
            ];
            $sections = PageColumnSection::where('page', $section)
                ->with([
                    'columns' => fn($q) => $q->orderBy('position')
                ])
                ->orderBy('position')
                ->get();

            $shared['sections'] = [];
            foreach ($sections as $section) {
                $shared['sections'][$section->position] = [
                    'name' => $section->name,
                    'icon' => $section->icon,
                    'page' => $section->page,
                    'slug' => $section->slug,
                    'columns' => $section->columns->map(function ($model) {
                        unset($model->section_id);
                        return $model;
                    })
                ];
            }
        }

        return $this->view($view, $shared, $scripts);
    }

    /**
     * Render the specified view
     *
     * @param string $view
     * @param array $shared
     * @param array $scripts
     * @return Response
     */
    protected function view(string $view, array $shared = [], array $scripts = []): Response
    {
        $response = Inertia::render($view, array_merge_recursive([
            'images' => [
                'upload' => '/images/upload.png'
            ],
            'menu' => $this->buildSideMenu(),
            'routes' => [
                'dashboard' => [
                    'index' => route('dashboard.index'),
                ],
                'resource' => [
                    'image' => [
                        'destroy' => route('api.image.destroy')
                    ]
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
        ], $shared));

        if (!empty($scripts)) {
            $response->withViewData($scripts);
        }

        return $response;
    }

    /**
     * Build side menu list
     *
     * @return array
     */
    protected function buildSideMenu(): array
    {
        $routes = $this->getDashboardRoutes();
        $side_menu = AdminMenu::select(['name', 'route', 'img', 'section'])
            ->whereNull('parent_id')
            ->orderBy('section')
            ->orderBy('position')
            ->get()
            ->map(
                fn($model) => str_starts_with($model->route, 'http') || (isset($routes[$model->route]))
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
     * Retrieve a list of dashboard menu methods with actions
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

    /**
     * Get top menu structure
     * @param Request $request
     * @return iterable
     */
    protected function topMenu(Request $request): iterable
    {
        $url = rtrim(preg_replace('/\d+/', ':id', $request->getRequestUri()), '/');

        $parent = AdminMenu::where('route', 'like', $url . '%')->first()?->parent;

        return $parent?->subMenus()
            ->select('name', 'route', 'img')
            ->orderBy('position')
            ->get()
            ->map(function ($model) use ($url) {
                $model->active = $model->route === $url;
                return $model;
            }) ?? [];
    }
}