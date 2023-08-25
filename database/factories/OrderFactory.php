<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'       => User::find(4)->id,
            'order_code'    => idate('U'),
            'ticket_id'     => Ticket::all()->random()->id,
            'trip_type'     => 'Pulang-Pergi',
            'go_date'       => fake()->date(),
            'back_date'     => fake()->date(),
            'amount'        => 2,
        ];
    }
}
