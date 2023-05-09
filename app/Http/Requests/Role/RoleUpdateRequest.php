<?php

namespace App\Http\Requests\Role;

use App\Http\Requests\DefaultRequest;

class RoleUpdateRequest extends DefaultRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'level' => ['required', 'numeric', 'min:0', 'max:255'],
            'name' => ['required', 'string']
        ];
    }
}