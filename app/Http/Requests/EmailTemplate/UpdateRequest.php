<?php

namespace App\Http\Requests\EmailTemplate;

use App\Http\Requests\DefaultRequest;
use App\Models\EmailTemplate;
use Illuminate\Validation\Rule;

class UpdateRequest extends DefaultRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        // Get user id from request query
        $id = $this->route()->parameter('template');
        if (!is_numeric($id)) {
            $id = $id->id;
        }
        return [
            'title' => ['nullable', 'string'],
            'slug' => ['nullable', 'string', Rule::unique(EmailTemplate::class)->ignore($id)],
            'subject' => ['nullable', 'string'],
            'variables' => ['nullable', 'array'],
            'body' => ['nullable', 'array']
        ];
    }
}