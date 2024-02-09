<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\BaseDashboardController;
use App\Models\EmailTemplate;
use Inertia\Response;

class EmailTemplateController extends BaseDashboardController
{
    /**
     * Email Template creation page
     *
     * @return Response
     */
    public function create(): Response
    {
        return $this->view(
            view: 'EmailTemplates/Index',
            shared: [
                'breadcrumbs' => [
                    [
                        'name' => 'Settings',
                        'url' => route('dashboard.settings.index')
                    ],
                    [
                        'name' => 'Emails'
                    ]
                ],
                'routes' => [
                    'save' => route('api.templates.store')
                ]
            ],
            scripts: [
                'css' => [
                    'resources/assets/css/admin/content-table.scss',
                    'resources/assets/css/admin/email-editor.scss'
                ]
            ]
        );
    }

    /**
     * Email Template edit page
     *
     * @param EmailTemplate $template
     * @return Response
     */
    public function edit(EmailTemplate $template): Response
    {
        return $this->view(
            view: 'EmailTemplates/Index',
            shared: [
                'breadcrumbs' => [
                    [
                        'name' => 'Settings',
                        'url' => route('dashboard.settings.index')
                    ],
                    [
                        'name' => 'Emails'
                    ]
                ],
                'model' => $template,
                'routes' => [
                    'save' => route('api.templates.update', $template->id)
                ]
            ],
            scripts: [
                'css' => [
                    'resources/assets/css/admin/content-table.scss',
                    'resources/assets/css/admin/email-editor.scss'
                ]
            ]
        );
    }
}
