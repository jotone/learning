<?php

namespace App\Http\Requests\SocialMediaLink;

use App\Http\Requests\DefaultRequest;

class SocialMediaSortRequest extends DefaultRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'list'   => ['required', 'array'],
            'list.*' => ['exists:social_media_links,id'],
        ];
    }
}