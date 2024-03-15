<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Resource\ImageDestroyRequest;
use Illuminate\Http\JsonResponse;

class ImageController extends BaseApiController
{
    /**
     * Thumbnail folders
     * @var array|string[]
     */
    protected array $folders = ['thumb_large', 'thumb_small'];

    /**
     * Remove image by its path
     *
     * @param ImageDestroyRequest $request
     * @return JsonResponse
     */
    public function destroy(ImageDestroyRequest $request): JsonResponse
    {
        // Get request data
        $input = $request->validated();
        // Check if image file exists
        if (file_exists(public_path($input['path']))) {
            $path_info = pathinfo($input['path']);
            unlink(public_path($input['path']));

            foreach ($this->folders as $folder) {
                // Get file path
                $file = public_path("{$path_info['dirname']}/{$folder}/{$path_info['basename']}");
                // Remove file if exists
                file_exists($file) && unlink($file);
            }
        }
        
        if (
            !empty($input['entity'])
            && !empty($input['entity_id'])
            && !empty($input['field'])
            && class_exists($input['entity'])
        ) {
            $input['entity']::where('id', $input['entity_id'])->update([
                $input['field'] => null
            ]);
        }

        return response()->json(null, 204);
    }
}