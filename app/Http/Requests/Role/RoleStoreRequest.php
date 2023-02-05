<?php

namespace App\Http\Requests\Role;

use App\Http\Requests\DefaultRequest;

class RoleStoreRequest extends DefaultRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'slug' => ['required', 'string'],
            'level' => ['required', 'numeric', 'min:0', 'max:255']
        ];
    }

    /**
     * Prepare the data for validation.
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => $this->request->has('slug')
                ? $this->request->get('slug')
                : generateUrl($this->request->get('name'))
        ]);
    }
}
