<?php

namespace App\Http\Requests\Export;

use App\Http\Requests\DefaultRequest;

class CourseExportRequest extends DefaultRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'list' => ['nullable', 'array'],
            'list.*' => ['required', 'exists:courses,id']
        ];
    }
}