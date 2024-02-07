<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class BaseApiController extends Controller
{


    /**
     * Update the simple model
     *
     * @param Model $model
     * @param FormRequest $request
     * @return JsonResponse
     */
    protected function updateSimpleModel(Model $model, FormRequest $request): JsonResponse
    {
        $args = $request->validated();

        DB::beginTransaction();

        try {
            foreach ($args as $key => $val) {
                $model->{$key} = $val;
            }

            // Update entity
            $model->isDirty() && $model->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return $this->serverError($e);
        }

        return response()->json($model);
    }
}
