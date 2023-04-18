<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BasicApiController;
use App\Http\Requests\SocialMediaLink\SocialMediaLinkRequest;
use App\Http\Requests\SocialMediaLink\SocialMediaSortRequest;
use App\Models\SocialMediaLink;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SocialMediaLinkController extends BasicApiController
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
     * Set new positions to social media links
     *
     * @param SocialMediaSortRequest $request
     * @return JsonResponse
     */
    public function sort(SocialMediaSortRequest $request): JsonResponse
    {
        $args = $request->validated();

        foreach ($args['list'] as $position => $id) {
            SocialMediaLink::where('id', $id)->update(['position' => $position]);
        }

        return response()->json(SocialMediaLink::orderBy('position')->get());
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