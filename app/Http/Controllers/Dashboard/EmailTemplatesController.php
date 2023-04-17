<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\BasicAdminController;
use Illuminate\Http\Request;
use Inertia\Response;

class EmailTemplatesController extends BasicAdminController
{
    /**
     * Create Email Template form
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        return $this->form('EmailTemplates/Form', $request, [
            'routes' => [
                'email' => [
                    'store' => '#'
                ]
            ],
            'styles' => [
                '/js/ckeditor/contents.css'
            ],
            'scripts' => [
                '/js/ckeditor/ckeditor.js',
            ]
        ]);
    }

    public function edit()
    {

    }
}