<?php

namespace App\Http\Requests\Role;

use App\Http\Requests\DefaultRequest;

class RoleBulkDeleteRequest extends DefaultRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'ids' => ['array'],
            'ids.*' => ['exists:roles,id']
        ];
    }
}