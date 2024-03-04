<?php

namespace App\Http\Requests\PageColumn;

use App\Http\Requests\DefaultRequest;

class SortRequest extends DefaultRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return $this->sortRequestRules('page_columns');
    }
}