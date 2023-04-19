<?php

namespace App\Http\Requests\EmailTemplate;

class EmailTemplateUpdateRequest extends EmailTemplateStoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['slug'] = ['nullable', 'string', 'alpha_dash'];
        return $rules;
    }

    /**
     * Prepare the data for validation.
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge(['variables' => $this->variables()]);
    }
}