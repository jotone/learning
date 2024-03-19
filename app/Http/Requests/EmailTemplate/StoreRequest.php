<?php

namespace App\Http\Requests\EmailTemplate;

use App\Services\Str;
use App\Http\Requests\DefaultRequest;
use App\Models\EmailTemplate;

class StoreRequest extends DefaultRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string'],
            'slug' => ['required', 'string', 'unique:email_templates,slug'],
            'subject' => ['nullable', 'string'],
            'variables' => ['nullable', 'array'],
            'body' => ['nullable', 'array']
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        if (!$this->request->has('slug')) {
            $slug = Str::generateUrl($this->request->get('title'));
            if (EmailTemplate::where('slug', $slug)->count()) {
                $slug .= '-' . uniqid();
            }
            $this->merge([
                'slug' => $slug
            ]);
        }
    }
}