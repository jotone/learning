<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class BaseApiController extends Controller
{
    /**
     * Update model positions
     *
     * @param string $class
     * @param FormRequest $request
     * @param string $field
     * @return JsonResponse
     */
    protected function simpleSortRequest(string $class, FormRequest $request, string $field = 'position'): JsonResponse
    {
        $input = $request->validated();

        DB::beginTransaction();

        try {
            foreach ($input['list'] as $item) {
                $model = $class::findOrFail($item['id']);
                $model->{$field} = $item['position'];
                $model->save();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->serverError($e);
        }

        return response()->json($input['list']);
    }

    /**
     * Update the model by simple algorithm
     *
     * @param Model $model
     * @param FormRequest $request
     * @return JsonResponse
     */
    protected function simpleUpdateRequest(Model $model, FormRequest $request): JsonResponse
    {
        $args = $request->validated();

        DB::beginTransaction();

        try {
            foreach ($args as $key => $val) {
                $model->$key = $val;
            }

            // Update entity
            $model->isDirty() && $model->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->serverError($e);
        }

        return response()->json($model);
    }
}
