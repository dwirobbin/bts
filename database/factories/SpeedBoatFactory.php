<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SpeedBoat>
 */
class SpeedBoatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $names = ['Awara'];
        $statuses = ['Ready', 'Dalam Perjalanan Berangkat', 'Dalam Perjalanan Kembali Pulang'];

        return [
            'name' => fake()->unique()->randomElement($names),
            'owner_id' => 2,
            'status' => fake()->optional(0.8, NULL)->randomElement($statuses),
        ];
    }
}
