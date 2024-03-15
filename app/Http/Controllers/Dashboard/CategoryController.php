<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\BaseDashboardController;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Response;

class CategoryController extends BaseDashboardController
{
    /**
     * Returns the page for an editing category
     *
     * @param Category $category
     * @return Response
     */
    public function edit(Category $category): Response
    {
        return $this->view(
            view: 'Category/Form/Index',
            shared: [
                'breadcrumbs' => [
                    ['name' => 'Courses', 'url' => route('dashboard.courses.index')],
                    ['name' => 'Edit Category']
                ],
                'model' => $category,
                'routes' => [
                    'api' => route('graphql.category')
                ]
            ]);
    }
}