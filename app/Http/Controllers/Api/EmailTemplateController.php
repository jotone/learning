<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\EmailTemplate\{StoreRequest, UpdateRequest};
use App\Models\EmailTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class EmailTemplateController extends BaseApiController
{
    /**
     * Create an email template entity
     *
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $input = $request->validated();

        DB::beginTransaction();

        try {
            $template = EmailTemplate::create($input);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->serverError($e);
        }
        return response()->json($template, 201);
    }

    /**
     * Update the specified email template entity
     *
     * @param EmailTemplate $template
     * @param UpdateRequest $request
     * @return JsonResponse
     */
    public function update(EmailTemplate $template, UpdateRequest $request): JsonResponse
    {
        return $this->simpleUpdateRequest($template, $request);
    }

    /**
     * Remove the specified email template
     *
     * @param EmailTemplate $template
     * @return JsonResponse
     */
    public function destroy(EmailTemplate $template): JsonResponse
    {
        $template->delete();
        return response()->json(null, 204);
    }
}