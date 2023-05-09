<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\BasicAdminController;
use App\Models\EmailTemplate;
use App\Models\Settings;
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
        return $this->form(
            template: 'EmailTemplates/Form',
            request: $request,
            share: [
                'routes' => [
                    'email' => [
                        'form' => route('api.email-templates.store'),
                    ]
                ],
                'scripts' => [
                    '/js/ckeditor/ckeditor.js',
                ]
            ]
        );
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
        return $this->form(
            template: 'EmailTemplates/Form',
            request: $request,
            share: [
                'model' => $template,
                'routes' => [
                    'email' => [
                        'form' => route('api.email-templates.update', $template->id)
                    ]
                ],
                'scripts' => [
                    '/js/ckeditor/ckeditor.js',
                ]
            ]
        );
    }
}