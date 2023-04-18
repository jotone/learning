<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\BasicAdminController;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Inertia\Response;

class EmailTemplatesController extends BasicAdminController
{
    /**
     * Create Email Template form
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        return $this->form('EmailTemplates/Form', $request, [
            'routes'  => [
                'email' => [
                    'form' => route('api.email-templates.store'),
                ]
            ],
            'scripts' => [
                '/js/ckeditor/ckeditor.js',
            ]
        ]);
    }

    /**
     * Edit Email Template form
     *
     * @param EmailTemplate $template
     * @param Request $request
     * @return Response
     */
    public function edit(EmailTemplate $template, Request $request): Response
    {
        return $this->form('EmailTemplates/Form', $request, [
            'routes'  => [
                'email' => [
                    'form' => route('api.email-templates.update', $template->id)
                ]
            ],
            'model'   => $template,
            'scripts' => [
                '/js/ckeditor/ckeditor.js',
            ]
        ]);
    }
}