<?php

namespace App\Http\Requests\SocialMedia;

use App\Enums\SocialMedia;
use App\Http\Requests\DefaultRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends DefaultRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'type' => ['nullable', Rule::in(array_values(SocialMedia::forSelect()))],
            'caption' => ['nullable', 'string'],
            'icon' => ['nullable', 'string'],
            'link' => ['nullable', 'string'],
            'position' => ['nullable', 'numeric']
        ];
    }
}