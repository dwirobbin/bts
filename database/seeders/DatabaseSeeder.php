<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\OrderSeeder;
use Database\Seeders\StreetSeeder;
use Database\Seeders\TicketSeeder;
use Database\Seeders\AirlineSeeder;
use Database\Seeders\PassengerSeeder;
use Database\Seeders\TransactionSeeder;
use Database\Seeders\PaymentMethodSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            AirlineSeeder::class,
            StreetSeeder::class,
            PaymentMethodSeeder::class,
            TicketSeeder::class,
            OrderSeeder::class,
            TransactionSeeder::class,
            PassengerSeeder::class,

        ]);
    }
}
