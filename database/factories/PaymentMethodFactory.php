<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentMethod>
 */
class PaymentMethodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $methods = ['Gopay', 'Dana'];

        return [
            'method' => fake()->unique()->randomElement($methods),
            'target_account' => fake()->randomNumber(9),
            'created_by' => 2
        ];
    }
}
