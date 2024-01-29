<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\{SocialMediaStoreRequest, SocialMediaUpdateRequest};
use App\Models\SocialMedia;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SocialMediaController extends Controller
{
    /**
     * Create a social media entity
     *
     * @param SocialMediaStoreRequest $request
     * @return JsonResponse
     */
    public function store(SocialMediaStoreRequest $request): JsonResponse
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
     * @param SocialMediaUpdateRequest $request
     * @return JsonResponse
     */
    public function update(SocialMedia $social, SocialMediaUpdateRequest $request): JsonResponse
    {
        $input = $request->validated();

        DB::beginTransaction();

        try {
            foreach ($input as $key => $val) {
                $social->{$key} = $val;
            }

            // Update entity
            $social->isDirty() && $social->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->serverError($e);
        }

        return response()->json($social);
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