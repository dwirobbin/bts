<?php

namespace App\Http\Requests;

use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRequest extends FormRequest
{
    protected static $NEEDS_AUTHORIZATION = true;
    protected static $ERROR_MESSAGES      = [
        'required'  => ':attribute tidak boleh kosong.',
        'string'    => ':attribute harus berupa string.',
        'numeric'   => ':attribute harus berupa angka.',
        'min'       => ':attribute minimal berisi :min karakter.',
        'email'     => ':attribute harus diisi dengan format email yang benar.',
        'unique'    => ':attribute sudah ada.',
        'confirmed' => ':attribute tidak sama.',
        'in'        => ':attribute yang dipilih tidak benar.',
    ];
    protected static $ATTRIBUTE_NAMES     = [
        'name'                  => 'Nama',
        'email'                 => 'Email',
        'gender'                => 'Jenis Kelamin',
        'phone_number'          => 'No. Hp',
        'password'              => 'Password',
        'password_confirmation' => 'Password Konfirmasi',
        'role_id'               => 'Role',
    ];

    /**
     * Determine if the user is authorized to make this request.
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
        if (request()->isMethod('POST')) {
            $emailUniqueRule = Rule::unique('users', 'email')->where(fn ($q) => $q->where('email', $this->email));
            $phoneNumberUniqueRule = Rule::unique('users', 'phone_number')->where(fn ($q) => $q->where('phone_number', $this->phone_number));
            $passwordRule = ['required', 'string'];
        } else if (request()->isMethod('PUT')) {
            $emailUniqueRule = Rule::unique('users', 'email')->ignore($this->email, 'email');
            $phoneNumberUniqueRule = Rule::unique('users', 'phone_number')->ignore($this->phone_number, 'phone_number');
            $passwordRule = ['nullable'];
        }

        return [
            'name'                  => ['required', 'string'],
            'email'                 => ['required', 'string', $emailUniqueRule],
            'gender'                => ['required', 'in:Laki-laki,Perempuan'],
            'phone_number'          => ['required', 'numeric', $phoneNumberUniqueRule],
            'password'              => $passwordRule,
            'password_confirmation' => $passwordRule,
            'role_id'               => [
                Rule::when(preg_match('[driver|customer]', auth()->user()->role->name), 'nullable'),
                'integer'
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
