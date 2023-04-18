<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BasicApiController;
use App\Http\Requests\EmailTemplate\EmailTemplateStoreRequest;
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