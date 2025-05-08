<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define users to seed
        $users = [
            [
                'email' => 'test@test.com',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'password' => Hash::make('password123'),
                'discount_id' => null,
                'card_id' => null,
                'user_role' => 1, 
            ]
        ];

        // Create users and generate tokens
        foreach ($users as $userData) {
            $user = User::create($userData);

            // Generate a personal access token for the user
            $user->createToken('default-token');
        }

        $trains = DB::table('trains')->get();

        // Iterate over each train and create tickets
        foreach ($trains as $train) {
            // Fetch all stations in the train's route
            $routeStations = DB::table('routes')
                ->where('train_id', $train->id)
                ->orderBy('sequence_number') // Assuming 'order' defines the sequence of stations
                ->pluck('station_id')
                ->toArray();

            if (count($routeStations) < 2) {
                // Skip trains with less than 2 stations in their route
                continue;
            }

            // Randomly select a start and end station ensuring start comes before end
            $startStationIndex = rand(0, count($routeStations) - 2);
            $endStationIndex = rand($startStationIndex + 1, count($routeStations) - 1);

            $startStation = $routeStations[$startStationIndex];
            $endStation = $routeStations[$endStationIndex];

            // Insert a valid ticket
            DB::table('tickets')->insert([
                'train_id' => $train->id,
                'user_id' => $user->id,
                'start_station' => $startStation,
                'end_station' => $endStation,
                'platny_od' => now()->addDays(rand(1, 30)), // Random date within the next 30 days
                'platny_do' => now()->addDays(rand(31, 60)), // Random date within the next 60 days
            ]);
        }
    }
}
