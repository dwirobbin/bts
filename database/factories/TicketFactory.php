<?php

namespace Database\Factories;

use App\Models\Street;
use App\Models\SpeedBoat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'speedboat_id'    => fake()->numberBetween(1, SpeedBoat::count()),
            'street_id'     => fake()->numberBetween(1, Street::count()),
            'go_price'      => fake()->optional(1)->numberBetween(80000, 150000),
            'goback_price'  => fake()->optional(0.4)->numberBetween(150000, 285000),
            'stock'         => fake()->optional(0.7, 0)->numberBetween(0, 100),
        ];
    }
}
