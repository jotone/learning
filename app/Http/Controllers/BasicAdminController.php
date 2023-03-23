<?php

namespace App\Http\Controllers;

use App\Models\AdminMenu;
use App\Models\Settings;
use Illuminate\Http\Request;
use Inertia\{Inertia, Response};

class BasicAdminController extends Controller
{
    protected array $order = [
        'by'  => 'created_at',
        'dir' => 'desc'
    ];

    /**
     * Default page render.
     *   Adds to rendered vue component default variables: routes, admin side menu,
     *   top menu, then merges them with shared
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
                    'order'  => [
                        'by'  => $this->order['by'],
                        'dir' => $this->order['dir']
                    ],
                    'page'   => $request->get('page', 1),
                    'search' => $request->get('search', ''),
                    'take'   => $this->take
                ]
            ], $share)
        );
    }

    protected function form(string $template, Request $request, array $share = []): Response
    {
        $path_info = preg_replace('/(create|edit\/\d+)/', '', $request->getPathInfo());
        $parent_menu = $this->getMenuParent(AdminMenu::firstWhere(['route' => rtrim($path_info, '/')]));

        return Inertia::render($template, array_merge_recursive(
            [
                'menu'    => AdminMenu::select(['name', 'route', 'img', 'is_top'])
                    ->whereNull('parent_id')
                    ->orderBy('position')
                    ->get()
                    ->map(function ($model) {
                        $image = public_path('images/icons/' . $model->img . '.svg');
                        $model->img = file_exists($image) ? file_get_contents($image) : null;
                        return $model;
                    }),
                'routes'  => [
                    'auth'      => [
                        'logout' => route('auth.logout')
                    ],
                    'dashboard' => [
                        'index' => route('dashboard.index', [], false)
                    ]
                ],
                'settings' => Settings::where('section', 'hidden')->get()->pluck('value', 'key')->toArray(),
                'topMenu' => AdminMenu::select(['name', 'route'])
                    ->where('parent_id', $parent_menu->id)
                    ->orderBy('position')
                    ->get()
            ],
            $share
        ));
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
}