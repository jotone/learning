<?php

namespace App\Http\Requests\User;

use App\Http\Requests\DefaultRequest;
use App\Models\User;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends DefaultRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        // Get user id from request query
        $id = $this->route()->parameter('user');
        if (!is_numeric($id)) {
            $id = $id->id;
        }

        $rules = [
            'first_name'     => ['nullable', 'string'],
            'last_name'      => ['nullable', 'string'],
            'email'          => ['nullable', 'email', Rule::unique(User::class)->ignore($id)],
            'img_url'        => ['nullable', 'mimes:jpeg,png,jpg'],
            'about'          => ['nullable', 'string'],
            'status'         => ['nullable', 'numeric', 'min:0', 'max:' . count(config('enums.user.statuses'))],
            'role_id'        => ['nullable', 'exists:roles,id'],
            'timezone'       => ['nullable', 'string'],
            'country'        => ['nullable', 'string'],
            'region'         => ['nullable', 'string'],
            'city'           => ['nullable', 'string'],
            'address'        => ['nullable', 'string'],
            'ext_addr'       => ['nullable', 'string'],
            'zip'            => ['nullable', 'string'],
            'phone'          => ['nullable', 'string'],
            'shirt_size'     => ['nullable', 'numeric', 'min: 0', 'max:' . count(config('enums.user.shirt_sizes'))],
            'signature'      => ['nullable'], // TODO
            'signature_ip'   => ['nullable'], // TODO
            'signature_date' => ['nullable'], // TODO
            'courses'        => ['nullable', 'array'] // TODO
        ];

        if ($this->request->has('password')) {
            $rules['password'] = ['required', 'string', 'min:8'];
            $rules['confirmation'] = ['required', 'string', 'same:password'];
        }

        return $rules;
    }
}