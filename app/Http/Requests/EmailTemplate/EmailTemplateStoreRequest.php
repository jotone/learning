<?php

namespace App\Http\Requests\EmailTemplate;

use App\Http\Requests\DefaultRequest;

class EmailTemplateStoreRequest extends DefaultRequest
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
            'slug' => ['required', 'string', 'alpha_dash'],
            'subject' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'footer_text' => ['nullable', 'string'],
            'styles' => ['nullable', 'string'],
            'variables' => ['nullable', 'array'],
        ];
    }

    /**
     * Prepare the data for validation.
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            // Create slug value from title if it does not exist
            'slug' => $this->request->has('slug')
                ? $this->request->get('slug')
                : generateUrl($this->request->get('name')),
            'variables' => $this->variables()
        ]);
    }

    protected function variables(): array
    {
        // Convert variables table
        $variables = json_decode($this->request->get('variables', '[]'), !0);

        // Check if array is associative
        if (array_is_list($variables)) {
            // The variables array is not associative
            $list = [];
            foreach ($variables as $variable) {
                $list[$variable['name']] = [$variable['entity'], $variable['field']];
            }
        } else {
            // The variables array is assoc
            $list = $variables;
        }

        return $list;
    }
}