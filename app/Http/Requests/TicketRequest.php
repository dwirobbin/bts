<?php

namespace App\Http\Requests;

use App\Models\Ticket;
use Illuminate\Http\Response;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class TicketRequest extends FormRequest
{
    protected static $NEEDS_AUTHORIZATION = true;
    protected static $ERROR_MESSAGES      = [
        'required'  => ':attribute wajib diisi.',
        'numeric'    => ':attribute harus berupa angka.',
    ];
    protected static $ATTRIBUTE_NAMES     = [
        'speedboat_id'   => 'Speed Boat',
        'street_id'   => 'Rute',
        'hours_of_departure' => 'Jam keberangkatan',
        'price'   => 'Harga',
        'stock'   => 'Stok',
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
        if (request()->isMethod('POST')) {
            $speedboatIdRule = ['required'];
            $streetIdRule = ['required'];
        } elseif (request()->isMethod('PUT')) {
            $speedboatIdRule = ['nullable'];
            $streetIdRule = ['nullable'];
        }

        return [
            'speedboat_id'    => $speedboatIdRule,
            'street_id'     => $streetIdRule,
            'hours_of_departure' => ['required'],
            'price'         => ['required', 'numeric'],
            'stock'         => ['required', 'numeric'],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'price' => str_replace('.', '', $this->price),
        ]);
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

    /**
     * Handle a passed validation attempt.
     */
    public function passedValidation(): void
    {
        if (request()->isMethod('POST')) {
            $sameTicket = Ticket::whereSpeedboatId($this->speedboat_id)->whereStreetId($this->street_id)->first();

            if ($sameTicket) {
                throw new HttpResponseException(response()->json(
                    ['same_ticket' => 'Tiket sudah ada di database!'],
                    Response::HTTP_CONFLICT
                ));
            }
        };
    }
}
