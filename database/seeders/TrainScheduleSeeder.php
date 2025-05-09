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
        $trainBaseName = 'Ex TATRAN';
        $startingTrainNumber = 601;
        $trainsPerDay = 18; // Number of trains to generate per day
        $hoursBetweenTrains = 1; // Hours between consecutive trains
        
        // Base stations and their initial departure times
        $baseStations = [
            ['name' => 'Bratislava hl.st.', 'departure_time' => '03:27:00'],
            ['name' => 'Bratislava Vinohrady', 'departure_time' => '03:34:00'],
            ['name' => 'Trnava', 'departure_time' => '04:01:00'],
            ['name' => 'Trenčín', 'departure_time' => '04:38:00'],
            ['name' => 'Púchov', 'departure_time' => '04:57:00'],
            ['name' => 'Považská Bystrica', 'departure_time' => '05:07:00'],
            ['name' => 'Žilina', 'departure_time' => '05:33:00'],
            ['name' => 'Vrútky', 'departure_time' => '05:56:00'],
            ['name' => 'Kraľovany', 'departure_time' => '06:10:00'],
            ['name' => 'Ružomberok', 'departure_time' => '06:25:00'],
            ['name' => 'Liptovský Mikuláš', 'departure_time' => '06:47:00'],
            ['name' => 'Štrba', 'departure_time' => '07:16:00'],
            ['name' => 'Poprad-Tatry', 'departure_time' => '07:33:00'],
            ['name' => 'Spišská Nová Ves', 'departure_time' => '07:58:00'],
            ['name' => 'Margecany', 'departure_time' => '08:26:00'],
            ['name' => 'Kysak', 'departure_time' => '08:50:00'],
            ['name' => 'Košice', 'departure_time' => '09:17:00'],
        ];

        // Generate schedules for each day of the next month
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            // Create multiple trains for each day
            for ($trainIndex = 0; $trainIndex < $trainsPerDay; $trainIndex++) {
                $trainNumber = $startingTrainNumber + ($trainIndex * 2);
                $trainName = "Ex {$trainNumber} TATRAN";
                
                // Create a new train
                $train = Train::create(['name' => $trainName]);
                
                // Adjust departure times based on train index
                $stations = $this->adjustDepartureTimes($baseStations, $trainIndex, $hoursBetweenTrains);
                
                foreach ($stations as $index => $station) {
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

    /**
     * Adjust departure times for each train based on its index in the sequence
     */
    private function adjustDepartureTimes($stations, $trainIndex, $hoursBetweenTrains)
    {
        $adjustedStations = [];
        
        foreach ($stations as $station) {
            $departureTime = Carbon::createFromFormat('H:i:s', $station['departure_time']);
            $departureTime->addHours($trainIndex * $hoursBetweenTrains);
            
            $adjustedStations[] = [
                'name' => $station['name'],
                'departure_time' => $departureTime->format('H:i:s'),
            ];
        }
        
        return $adjustedStations;
    }
}
