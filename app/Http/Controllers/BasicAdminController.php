<?php

namespace App\Http\Controllers;

use App\Models\AdminMenu;
use Inertia\{Inertia, Response};

class BasicAdminController
{
    protected function view(string $template, array $share = []): Response
    {
        return Inertia::render($template, [
            'routes' => [
                'dashboard' => [
                    'index' => route('dashboard.index', [], false)
                ]
            ],
            'menu'   => AdminMenu::select(['name', 'route', 'img', 'is_top'])
                ->whereNull('parent_id')
                ->orderBy('position')
                ->get()
                ->map(function ($model) {
                    $image = public_path('images/icons/' . $model->img . '.svg');
                    $model->img = file_exists($image) ? file_get_contents($image) : null;
                    return $model;
                })
        ]);
    }
}