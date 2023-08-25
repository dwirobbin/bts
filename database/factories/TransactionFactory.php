<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Order;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id'          => Order::all()->random()->id,
            'paymentmethod_id'  => PaymentMethod::all()->random()->id,
            'name_account'      => User::find(4)->name,
            'from_account'      => '11210910000039',
            'total'             => 195000,
            'image'             => null,
            'status'            => false,
        ];
    }
}
