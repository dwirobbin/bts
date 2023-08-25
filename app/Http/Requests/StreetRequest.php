<?php

namespace App\Http\Requests;

use Illuminate\Http\Response;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StreetRequest extends FormRequest
{
    protected static $NEEDS_AUTHORIZATION = true;
    protected static $ERROR_MESSAGES      = [
        'required'  => ':attribute wajib diisi.',
        'string'    => ':attribute harus berupa string.',
    ];
    protected static $ATTRIBUTE_NAMES     = [
        'from_route'   => 'Lokasi Berangkat',
        'to_route'   => 'Lokasi Tujuan',
    ];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return self::$NEEDS_AUTHORIZATION;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
        return static::$ERROR_MESSAGES;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return static::$ATTRIBUTE_NAMES;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'from_route'    => ['required', 'string'],
            'to_route'      => ['required', 'string'],
        ];
    }

    /**
     * @overrride
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json(
            ['errors' => $validator->errors()->all()],
            Response::HTTP_UNPROCESSABLE_ENTITY
        ));
    }
}
