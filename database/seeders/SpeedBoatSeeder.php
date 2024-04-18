<?php

namespace Database\Seeders;

use App\Models\SpeedBoat;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SpeedBoatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SpeedBoat::factory(1)->create();
    }
}
