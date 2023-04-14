<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BasicApiController;
use App\Http\Requests\SocialMediaLink\SocialMediaLinkRequest;
use App\Models\SocialMediaLink;
use Illuminate\Http\JsonResponse;

class SocialMediaLinksController extends BasicApiController
{
    /**
     * Create Social Media Link
     *
     * @param SocialMediaLinkRequest $request
     * @return JsonResponse
     */
    public function store(SocialMediaLinkRequest $request): JsonResponse
    {
        return response()->json(SocialMediaLink::create(array_merge(
            $request->validated(),
            [
                'position' => SocialMediaLink::count()
            ]
        )), 201);
    }

    /**
     * Update Social Media Link
     *
     * @param SocialMediaLink $social
     * @param SocialMediaLinkRequest $request
     * @return JsonResponse
     */
    public function update(SocialMediaLink $social, SocialMediaLinkRequest $request): JsonResponse
    {
        $social->update($request->validated());

        return response()->json($social);
    }

    /**
     * Remove Social Media Link
     *
     * @param SocialMediaLink $social
     * @return JsonResponse
     */
    public function destroy(SocialMediaLink $social): JsonResponse
    {
        $social->delete();

        return response()->json([], 204);
    }
}