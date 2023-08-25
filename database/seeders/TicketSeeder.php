<?php

namespace Database\Seeders;

use App\Models\Street;
use App\Models\Airline;
use App\Models\Ticket;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tickets = [
            [
                'airline_id'    => Airline::find(1)->id,
                'street_id'     => Street::find(1)->id,
                'hours_of_departure' => '14:00:00',
                'price'         => 100000,
                'stock'         => 50,
            ],
            [
                'airline_id'    => Airline::find(1)->id,
                'street_id'     => Street::find(2)->id,
                'hours_of_departure' => '12:15:00',
                'price'         => 100000,
                'stock'         => 100,
            ],
        ];

        foreach ($tickets as $ticket) {
            Ticket::create($ticket);
        }
    }
}
