<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    protected static $NEEDS_AUTHORIZATION = true;
    protected static $ERROR_MESSAGES      = [
        'required'  => ':attribute wajib diisi.',
        'numeric'   => ':attribute harus berupa angka.',
        'min'       => ':attribute minimal harus :min karakter.',
        'max'       => ':attribute minimal harus :max karakter.',
    ];
    protected static $ATTRIBUTE_NAMES     = [
        'ticket_data.ticket_id'   => 'Tiket',
        'ticket_data.trip_type'   => 'Jenis Perjalanan',
        'ticket_data.go_date'   => 'Tanggal Pergi',
        'ticket_data.back_date'   => 'Tanggal Pulang',
        'passenger_data.name.*'   => 'Nama Penumpang',
        'passenger_data.ktp_number.*'   => 'No. Ktp Penumpang',
        'passenger_data.gender.*'   => 'Jenis Kelamin Penumpang',
        'payment_data.total_price'   => 'Total Harga',
        'payment_data.paymentmethod_id'   => 'Metode Pembayaran',
        'payment_data.senderaccount_name'   => 'Nama Akun Pengirim',
        'payment_data.senderaccount_number'   => 'Nomor Pengirim',
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
        if (isset($this['ticket_data']['trip_type']) && $this['ticket_data']['trip_type'] === 'Pulang-Pergi') {
            $backDateRule = ['required'];
        } else {
            $backDateRule = ['nullable'];
        }

        return [
            'ticket_data.ticket_id'             => ['required'],
            'ticket_data.trip_type'             => ['required', 'string'],
            'ticket_data.go_date'               => ['required'],
            'ticket_data.back_date'             => $backDateRule,
            'passenger_data.name.*'             => ['required', 'string'],
            'passenger_data.ktp_number.*'       => ['required', 'min:16', 'max:20'],
            'passenger_data.gender.*'           => ['required', 'string', 'in:Laki-laki,Perempuan'],
            'payment_data.total_price'          => ['required', 'numeric'],
            'payment_data.paymentmethod_id'     => ['required'],
            'payment_data.senderaccount_name'   => ['required', 'string'],
            'payment_data.senderaccount_number' => ['required', 'numeric'],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'payment_data.total_price' => str_replace('.', '', $this->payment_data['total_price']),
        ]);
    }
}
