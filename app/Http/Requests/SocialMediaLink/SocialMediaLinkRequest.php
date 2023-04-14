<?php

namespace App\Http\Requests\SocialMediaLink;

use App\Http\Requests\DefaultRequest;

class SocialMediaLinkRequest extends DefaultRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'string'],
            'url'  => ['nullable', 'string'],
        ];
    }
}