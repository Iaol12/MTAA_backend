<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Route;
use App\Models\Train;
use App\Models\Station;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TrainScheduleSeeder extends Seeder
{
    /**
     * Define all train templates
     * Each template contains the train name pattern, schedule, and stations
     */
    private $trainTemplates = [
        [
            'name_pattern' => 'Ex %d TATRAN',
            'starting_number' => 601,
            'trains_per_day' => 18,
            'hours_between_trains' => 1,
            'stations' => [
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

                
            ]
        ],
        
        [
            'name_pattern' => 'Os %d SEDITA',
            'starting_number' => 800,
            'trains_per_day' => 12,
            'hours_between_trains' => 1,
            'stations' => [
                ['name' => 'Bratislava hl.st.', 'departure_time' => '04:55:00'],
                ['name' => 'Bratislava-Vajnory', 'departure_time' => '05:00:00'],
                ['name' => 'Ivanka pri Dunaji', 'departure_time' => '05:10:00'],
                ['name' => 'Bernolákovo', 'departure_time' => '05:20:00'],
                ['name' => 'Senec', 'departure_time' => '05:25:00'],
                ['name' => 'Reca', 'departure_time' => '05:35:00'],
                ['name' => 'Pusté Úľany', 'departure_time' => '05:50:00'],
                ['name' => 'Sládkovičovo', 'departure_time' => '06:00:00'],
                ['name' => 'Galanta', 'departure_time' => '06:20:00'],
            ]
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cache all stations in memory to avoid repeated queries
        $stationsMap = Station::pluck('id', 'name')->toArray();
        
        // Generate schedules for each day (setting to one week for performance)
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->addDays(7); // Change back to endOfMonth() for full month
        
        // Use a transaction to speed up inserts
        DB::beginTransaction();
        
        try {
            $trainData = [];
            $routeData = [];
            $trainId = DB::table('trains')->max('id') ?? 0;
            $trainId++;
            
            // For each train template
            foreach ($this->trainTemplates as $template) {
                for ($date = clone $startDate; $date->lte($endDate); $date->addDay()) {
                    $dateString = $date->toDateString();
                    
                    // Create each train for this day
                    for ($trainIndex = 0; $trainIndex < $template['trains_per_day']; $trainIndex++) {
                        $trainNumber = $template['starting_number'] + ($trainIndex * 2);
                        $trainName = sprintf($template['name_pattern'], $trainNumber);
                        
                        // Collect train data for bulk insert
                        $trainData[] = ['name' => $trainName];
                        $currentTrainId = $trainId++;
                        
                        // Adjust departure times based on train index
                        $stations = $this->adjustDepartureTimes(
                            $template['stations'], 
                            $trainIndex, 
                            $template['hours_between_trains']
                        );
                        
                        foreach ($stations as $index => $station) {
                            if (isset($stationsMap[$station['name']])) {
                                // Collect route data for bulk insert
                                $routeData[] = [
                                    'train_id' => $currentTrainId,
                                    'station_id' => $stationsMap[$station['name']],
                                    'sequence_number' => $index + 1,
                                    'departure_time' => $dateString . ' ' . $station['departure_time'],
                                ];
                            }
                        }
                        
                        // Batch insert routes every 1000 records to avoid memory issues
                        if (count($routeData) >= 1000) {
                            DB::table('trains')->insert($trainData);
                            DB::table('routes')->insert($routeData);
                            $trainData = [];
                            $routeData = [];
                        }
                    }
                }
            }
            
            // Insert any remaining data
            if (count($trainData) > 0) {
                DB::table('trains')->insert($trainData);
            }
            if (count($routeData) > 0) {
                DB::table('routes')->insert($routeData);
            }
            
            DB::commit();
            $this->command->info('Train schedules seeded successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error seeding train schedules: ' . $e->getMessage());
            throw $e;
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
