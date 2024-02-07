<?php

namespace App\Http\Requests\SocialMedia;

use App\Http\Requests\DefaultRequest;

class SortRequest extends DefaultRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'list' => ['required', 'array'],
            'list.*.id' => ['required', 'exists:social_media,id'],
            'list.*.position' => ['required', 'numeric']
        ];
    }
}