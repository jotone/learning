<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class DefaultRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Converts a value to a boolean representation.
     *
     * @param mixed $value
     * @return bool
     */
    protected function toBool(mixed $value): bool
    {
        return in_array($value === 0 ? false : $value, [1, '1', true, 'true', 'on'], true);
    }

    /**
     * Handle a failed validation attempt.
     * @param Validator $validator
     * @return void
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        $errors = (new ValidationException($validator))->errors();

        throw new HttpResponseException(
            response()->json(['errors' => $errors], Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }

    /**
     * Default rules for the sort request
     *
     * @param string $table
     * @return array[]
     */
    protected function sortRequestRules(string $table): array
    {
        return [
            'list' => ['required', 'array'],
            'list.*.id' => ['required', 'exists:' . $table . ',id'],
            'list.*.position' => ['required', 'numeric']
        ];
    }
}