<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\BasicAdminController;
use App\Models\{EmailTemplate, Settings, SocialMediaLink};
use Illuminate\Http\Request;
use Inertia\Response;

class EmailTemplatesController extends BasicAdminController
{
    /**
     * Email Settings page
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $content = Settings::whereIn('section', ['smtp-settings', 'email-settings'])->get()->keyBy('key');
        $content['smtp_password']->value = $content['smtp_password']->value
            ? md5($content['smtp_password']->value)
            : '';
        return $this->form(
            template: 'EmailTemplates/Index',
            request: $request,
            share: [
                'content' => $content,
                'routes' => [
                    'emails' => [
                        'create' => route('dashboard.settings.emails.create'),
                        'edit' => route('dashboard.settings.emails.edit', ':id'),
                        'destroy' => route('api.email-templates.destroy', ':id')
                    ],
                    'settings' => [
                        'update' => route('api.settings.update')
                    ],
                    'social' => [
                        'store' => route('api.socials.store'),
                        'update' => route('api.socials.update', ':id'),
                        'sort' => route('api.socials.sort'),
                        'destroy' => route('api.socials.destroy', ':id')
                    ]
                ],
                'social' => SocialMediaLink::orderBy('position')->get(),
                'templates' => EmailTemplate::select('id', 'name')->orderBy('created_at', 'desc')->get(),
                'translations' => [
                    'email_templates' => __('email_templates'),
                    'user' => [
                        'password' => __('user.password')
                    ]
                ]
            ]
        );
    }

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
                ],
                'translations'  => [
                    'course' => [
                        'single' => __('course.single')
                    ],
                    'email_templates' => __('email_templates'),
                    'settings' => [
                        'main' => __('settings.main')
                    ],
                    'student' => [
                        'single' => __('student.single')
                    ],
                    'user' => [
                        'fields' =>  __('user.fields')
                    ],
                    'variable' => __('variable')
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
                ],
                'translations'  => [
                    'course' => [
                        'single' => __('course.single')
                    ],
                    'email_templates' => __('email_templates'),
                    'settings' => [
                        'main' => __('settings.main')
                    ],
                    'student' => [
                        'single' => __('student.single')
                    ],
                    'user' => [
                        'fields' =>  __('user.fields')
                    ],
                    'variable' => __('variable')
                ]
            ]
        );
    }
}