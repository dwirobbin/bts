<?php

namespace App\Http\Requests;

use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PaymentMethodRequest extends FormRequest
{
    protected static $NEEDS_AUTHORIZATION = true;
    protected static $ERROR_MESSAGES      = [
        'required'  => ':attribute wajib diisi.',
        'string'    => ':attribute harus berupa string.',
        'unique'    => ':attribute sudah ada.',
        'numeric'   => ':attribute harus berupa angka.',
    ];
    protected static $ATTRIBUTE_NAMES     = [
        'method'   => 'Metode Pembayaran',
        'target_account'   => 'No. Rekening Tujuan',
        'owner_id'   => 'Nama Pemilik',
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
            'method'  => ['required', 'string'],
            'target_account' => [
                'required',
                'numeric',
                Rule::when(request()->isMethod('POST'), Rule::unique('payment_methods', 'target_account')->where(fn ($query) => $query->where('target_account', $this->target_account))),
                Rule::when(request()->isMethod('PUT'), Rule::unique('payment_methods', 'target_account')->ignore($this->target_account, 'target_account'))
            ],
            'owner_id' => [
                Rule::when(auth()->user()->role->name === 'admin', 'required'),
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
