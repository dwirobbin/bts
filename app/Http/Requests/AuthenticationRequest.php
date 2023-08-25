<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthenticationRequest extends FormRequest
{
    protected static $NEEDS_AUTHORIZATION = true;
    protected static $ERROR_MESSAGES      = [
        'required'  => ':attribute tidak boleh kosong',
        'string'    => ':attribute harus berupa string',
        'min'       => ':attribute harus minimal :min karakter',
        'email'     => 'ini bukan format :attribute yang benar',
        'unique'    => ':attribute sudah ada',
        'confirmed' => ':attribute tidak sama',
    ];
    protected static $ATTRIBUTE_NAMES     = [
        'name'                  => 'Nama',
        'email'                 => 'Email',
        'password'              => 'Passsword',
        'password_confirmation' => 'Passsword Konfirmasi',
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
        if (request()->is('auth/login')) {
            $nameRule       = ['nullable'];
            $emailRule      = ['required', 'email', 'min:3'];
            $passwordRule   = ['required', 'string'];
            $confirmPasswordRule   = ['nullable'];
        } else if (request()->is('auth/register')) {
            $nameRule       = ['required', 'string', 'min:3'];
            $emailRule      = ['required', 'email', 'min:3', 'unique:users,email'];
            $passwordRule   = ['required', 'string', 'confirmed', 'min:5'];
            $confirmPasswordRule   = ['required', 'string'];
        } else if (request()->is('auth/send-password-reset-link')) {
            $nameRule       = ['nullable'];
            $emailRule      = ['required', 'email', 'min:3', 'exists:users,email'];
            $passwordRule   = ['nullable'];
            $confirmPasswordRule   = ['nullable'];
        } else if (request()->is('auth/reset-password')) {
            $nameRule       = ['nullable'];
            $emailRule      = ['nullable'];
            $passwordRule   = ['required', 'string', 'confirmed', 'min:5'];
            $confirmPasswordRule   = ['required', 'string'];
        }

        return [
            'name'                  => $nameRule,
            'email'                 => $emailRule,
            'password'              => $passwordRule,
            'password_confirmation' => $confirmPasswordRule,
        ];
    }
}
