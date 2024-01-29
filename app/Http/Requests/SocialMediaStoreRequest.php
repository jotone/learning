<?php

namespace App\Http\Requests;

use App\Enums\SocialMedia;
use Illuminate\Validation\Rule;

class SocialMediaStoreRequest extends DefaultRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(array_values(SocialMedia::forSelect()))],
            'caption' => ['required', 'string'],
            'icon' => ['nullable', 'string']
        ];
    }
}
