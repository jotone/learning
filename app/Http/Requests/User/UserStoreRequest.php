<?php

namespace App\Http\Requests\User;

use App\Http\Requests\DefaultRequest;

class UserStoreRequest extends DefaultRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'first_name'   => ['required', 'string'],
            'last_name'    => ['nullable', 'string'],
            'email'        => ['required', 'email', 'unique:users,email'],
            'password'     => ['required', 'string', 'min:8'],
            'confirmation' => ['required', 'string', 'same:password'],
            'img_url'      => ['nullable', 'mimes:jpeg,png,jpg'],
            'about'        => ['nullable', 'string'],
            'status'       => ['nullable', 'numeric', 'min:0', 'max:' . count(config('enums.user.statuses'))],
            'role_id'      => ['nullable', 'exists:roles,id'],
            'timezone'     => ['nullable', 'string'],
            'country'      => ['nullable', 'string'],
            'region'       => ['nullable', 'string'],
            'city'         => ['nullable', 'string'],
            'address'      => ['nullable', 'string'],
            'ext_addr'     => ['nullable', 'string'],
            'zip'          => ['nullable', 'string'],
            'phone'        => ['nullable', 'string'],
            'shirt_size'   => ['nullable', 'numeric', 'min: 0', 'max:' . count(config('enums.user.shirt_sizes'))],
        ];
    }

    /**
     * Prepare the data for validation.
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => strlen($this->request->get('phone')) > 5 ? $this->request->get('phone') : null
        ]);
    }
}