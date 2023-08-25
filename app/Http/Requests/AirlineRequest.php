<?php

namespace App\Http\Requests;

use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AirlineRequest extends FormRequest
{
    protected static $NEEDS_AUTHORIZATION = true;
    protected static $ERROR_MESSAGES      = [
        'required'  => ':attribute wajib diisi.',
        'string'    => ':attribute harus berupa string.',
        'unique'    => ':attribute sudah ada.',
    ];
    protected static $ATTRIBUTE_NAMES     = [
        'name'   => 'Nama SpeedBoat',
        'owner_id'   => 'Pemilik SpeedBoat',
        'status'   => 'Status SpeedBoat',
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
            'name'  => [
                'required',
                'string',
                Rule::when(request()->isMethod('POST'), Rule::unique('airlines', 'name')->where(fn ($query) => $query->where('name', $this->name))),
                Rule::when(request()->isMethod('PUT'), Rule::unique('airlines', 'name')->ignore($this->name, 'name'))
            ],
            'owner_id' => [
                Rule::when(request()->isMethod('POST'), 'required'),
                Rule::when(request()->isMethod('PUT'), 'nullable'),
                'numeric'
            ],
            'status' => [
                'nullable',
                'string'
            ],
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
