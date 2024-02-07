<?php

namespace App\Http\Requests\SocialMedia;

use App\Enums\SocialMedia;
use App\Http\Requests\DefaultRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends DefaultRequest
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
