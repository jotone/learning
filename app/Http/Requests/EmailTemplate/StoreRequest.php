<?php

namespace App\Http\Requests\EmailTemplate;

use App\Http\Requests\DefaultRequest;

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
            'title' => ['required', 'string'],
            'slug' => ['required', 'string', 'unique:email_templates,slug'],
            'subject' => ['nullable', 'string'],
            'variables' => ['nullable', 'array'],
            'body' => ['nullable', 'array']
        ];
    }
}