<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Route;
use App\Models\Train;
use App\Models\Station;
use Carbon\Carbon;

class TrainScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define the train template
        $trainTemplate = [
            'name' => 'Express Train',
            'stations' => [
                ['name' => 'Žilina', 'departure_time' => '08:00:00'],
                ['name' => 'Pezinok', 'departure_time' => '09:00:00'],
                ['name' => 'Košice', 'departure_time' => '10:00:00'],
            ],
        ];

        // Generate schedules for each day of the next month
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            // Create a new train for each day
            $train = Train::create(['name' => $trainTemplate['name']]);

            foreach ($trainTemplate['stations'] as $index => $station) {
                // Find the station by name
                $stationModel = Station::where('name', $station['name'])->first();

                if ($stationModel) {
                    Route::create([
                        'train_id' => $train->id,
                        'station_id' => $stationModel->id,
                        'sequence_number' => $index + 1,
                        'departure_time' => $date->toDateString() . ' ' . $station['departure_time'],
                    ]);
                }
            }
        }
    }
}
