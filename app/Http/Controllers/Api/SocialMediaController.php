<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SocialMediaStoreRequest;
use App\Models\SocialMedia;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SocialMediaController extends Controller
{
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

    public function update()
    {

    }

    public function destroy()
    {

    }
}