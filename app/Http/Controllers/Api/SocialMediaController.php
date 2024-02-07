<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\SocialMedia\{SortRequest, StoreRequest, UpdateRequest};
use App\Models\SocialMedia;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SocialMediaController extends BaseApiController
{
    /**
     * Create a social media entity
     *
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $input = $request->validated();

        DB::beginTransaction();

        try {
            $social= SocialMedia::create(array_merge($input, [
                'position' => SocialMedia::count()
            ]));
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->serverError($e);
        }

        return response()->json($social, 201);
    }

    /**
     * Update the specified social media entity
     *
     * @param SocialMedia $social
     * @param UpdateRequest $request
     * @return JsonResponse
     */
    public function update(SocialMedia $social, UpdateRequest $request): JsonResponse
    {
        return $this->updateSimpleModel($social, $request);
    }

    /**
     * Update the social media positions
     *
     * @param SortRequest $request
     * @return JsonResponse
     */
    public function sort(SortRequest $request): JsonResponse
    {
        $input = $request->validated();

        foreach ($input['list'] as $item) {
            $social = SocialMedia::findOrFail($item['id']);
            $social->position = $item['position'];
            $social->save();
        }

        return response()->json($input['list']);
    }

    /**
     * Remove the specified social media
     *
     * @param SocialMedia $social
     * @return JsonResponse
     */
    public function destroy(SocialMedia $social): JsonResponse
    {
        $social->delete();
        // Reset positions
        SocialMedia::orderBy('position')->get()->each(function ($social, $index) {
            $social->position = $index;
            $social->save();
        });
        return response()->json(status: 204);
    }
}