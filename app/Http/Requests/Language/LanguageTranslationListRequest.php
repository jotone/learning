<?php

namespace App\Http\Requests\Language;

use App\Http\Requests\DefaultRequest;

class LanguageTranslationListRequest extends DefaultRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'files' => ['required', 'array'],
        ];
    }
}