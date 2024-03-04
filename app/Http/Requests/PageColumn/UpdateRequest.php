<?php

namespace App\Http\Requests\PageColumn;

use App\Http\Requests\DefaultRequest;

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
            'field' => ['nullable', 'string'],
            'name' => ['nullable', 'string'],
            'enable' => ['nullable', 'boolean'],
            'position' => ['nullable', 'numeric']
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        if ($this->request->has('enable')) {
            $this->merge(['enable' => $this->toBool($this->request->get('enable'))]);
        }
    }
}