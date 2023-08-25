<?php

namespace App\Http\Requests;

use App\Models\Ticket;
use Illuminate\Validation\Rule;
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
        'passenger_data.name.0'   => 'Nama Penumpang',
        'passenger_data.ktp_number.0'   => 'No. Ktp Penumpang',
        'passenger_data.gender.0'   => 'Jenis Kelamin Penumpang',
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
            'ticket_data.trip_type'             => ['required'],
            'ticket_data.go_date'               => ['required'],
            'ticket_data.back_date'             => $backDateRule,
            'passenger_data.name.0'             => ['required'],
            'passenger_data.ktp_number.0'       => ['required', 'min:16', 'max:20'],
            'passenger_data.gender.0'           => ['required'],
            'payment_data.total_price'          => ['required', 'numeric'],
            'payment_data.paymentmethod_id'     => ['required'],
            'payment_data.senderaccount_name'   => ['required'],
            'payment_data.senderaccount_number' => ['required', 'numeric'],
        ];
    }
}
