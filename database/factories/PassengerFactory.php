<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Passenger>
 */
class PassengerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = Faker::create('id_ID');

        return [
            'order_id' => Order::find(1)->id,
            'name'     => $faker->firstNameFemale() . ' ' . $faker->lastNameFemale(),
            'id_number' => $faker->nik(),
            'gender'    => 'Perempuan',
        ];
    }
}
