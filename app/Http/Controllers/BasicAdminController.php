<?php

namespace App\Http\Controllers;

use App\Models\AdminMenu;
use Illuminate\Http\Request;
use Inertia\{Inertia, Response};

class BasicAdminController
{
    protected string $order_by = 'created_at';

    protected string $order_dir = 'desc';

    protected int $take = 30;

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
    protected function view(string $template, Request $request, array $share = []): Response
    {
        $parent_menu = $this->getMenuParent(AdminMenu::firstWhere(['route' => rtrim($request->getPathInfo(), '/')]));

        $default = [
            'filters'  => [
                'order'  => [
                    'by' => $this->order_by,
                    'dir' => $this->order_dir
                ],
                'page'   => $request->get('page', 1),
                'search' => $request->get('search', ''),
                'take'   => $this->take
            ],
            'menu'     => AdminMenu::select(['name', 'route', 'img', 'is_top'])
                ->whereNull('parent_id')
                ->orderBy('position')
                ->get()
                ->map(function ($model) {
                    $image = public_path('images/icons/' . $model->img . '.svg');
                    $model->img = file_exists($image) ? file_get_contents($image) : null;
                    return $model;
                }),
            'routes'   => [
                'dashboard' => [
                    'index' => route('dashboard.index', [], false)
                ]
            ],
            'top_menu' => AdminMenu::select(['name', 'route'])
                ->where('parent_id', $parent_menu->id)
                ->orderBy('position')
                ->get()
        ];
        $default = array_merge_recursive($default, $share);
        return Inertia::render($template, $default);
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