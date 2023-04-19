<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BasicApiController;
use App\Http\Requests\EmailTemplate\{EmailTemplateStoreRequest, EmailTemplateUpdateRequest};
use App\Models\EmailTemplate;
use Illuminate\Http\JsonResponse;

class EmailTemplateController extends BasicApiController
{
    /**
     * Create EmailTemplate
     *
     * @param EmailTemplateStoreRequest $request
     * @return JsonResponse
     */
    public function store(EmailTemplateStoreRequest $request): JsonResponse
    {
        return response()->json(EmailTemplate::create($request->validated()), 201);
    }

    /**
     * Update EmailTemplate
     *
     * @param EmailTemplate $email_template
     * @param EmailTemplateUpdateRequest $request
     * @return JsonResponse
     */
    public function update(EmailTemplate $email_template, EmailTemplateUpdateRequest $request): JsonResponse
    {
        $email_template->update($request->validated());

        return response()->json($email_template);
    }

    /**
     * Remove EmailTemplate
     *
     * @param EmailTemplate $email_template
     * @return JsonResponse
     */
    public function destroy(EmailTemplate $email_template): JsonResponse
    {
        $email_template->delete();

        return response()->json([], 204);
    }
}