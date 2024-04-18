<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Order;
use App\Models\Passenger;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Faker\Factory as Faker;

class PassengerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        Passenger::query()->create([
            'order_id' => Order::query()->find(1)->id,
            'name'     => User::query()->find(4)->name,
            'id_number' => $faker->nik(),
            'gender'    => 'Laki-laki',
        ]);

        Passenger::factory(1)->create();
    }
}
