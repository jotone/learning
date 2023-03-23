<?php

namespace App\Http\Requests\Language;

use App\Http\Requests\DefaultRequest;
use Illuminate\Validation\Rule;
use LaravelLang\Publisher\Constants\Locales;

class LanguageStoreRequest extends DefaultRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'lang' => ['required', 'string', 'min:2', Rule::in(Locales::values())],
        ];
    }
}