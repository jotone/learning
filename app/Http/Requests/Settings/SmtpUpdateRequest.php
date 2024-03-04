<?php

namespace App\Http\Requests\Settings;

use App\Http\Requests\DefaultRequest;
use Illuminate\Validation\Rule;

class SmtpUpdateRequest extends DefaultRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'smtp_username' => ['required', 'string'],
            'smtp_password' => ['required', 'string'],
            'smtp_host' => ['required', 'string'],
            'smtp_port' => ['required', 'string'],
            'smtp_encryption' => ['required', Rule::in(['TLS', 'SSL', 'SSL-TLS'])],
            'smtp_from_address' => ['required', 'email'],
            'smtp_from_name' => ['nullable', 'string']
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'smtp_encryption' => strtoupper($this->request->get('smtp_encryption'))
        ]);
    }
}