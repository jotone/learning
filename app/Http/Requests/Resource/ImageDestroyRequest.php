<?php

namespace App\Http\Requests\Resource;

use App\Http\Requests\DefaultRequest;

class ImageDestroyRequest extends DefaultRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'path' => ['required', 'string'],
            'entity' => ['nullable', 'string'],
            'entity_id' => ['nullable', 'numeric'],
            'field' => ['nullable', 'string']
        ];
    }
}