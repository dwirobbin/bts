<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\{
    RoleSeeder,
    UserSeeder,
    OrderSeeder,
    StreetSeeder,
    TicketSeeder,
    SpeedBoatSeeder,
    PassengerSeeder,
    TransactionSeeder,
    PaymentMethodSeeder,
};

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
            SpeedBoatSeeder::class,
            StreetSeeder::class,
            PaymentMethodSeeder::class,
            TicketSeeder::class,
            // OrderSeeder::class,
            // TransactionSeeder::class,
            // PassengerSeeder::class,

        ]);
    }
}
