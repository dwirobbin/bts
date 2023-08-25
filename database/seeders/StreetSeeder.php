<?php

namespace Database\Seeders;

use App\Models\Street;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class StreetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $streets = [
            ['from_route' => 'Palembang', 'to_route' => 'Jalur 3'],
            ['from_route' => 'Jalur 3', 'to_route' => 'Palembang'],
        ];

        foreach ($streets as $street) {
            Street::create($street);
        }
    }
}
