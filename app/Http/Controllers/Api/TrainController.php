<?php
// filepath: d:\mta\MTAA_backend\app\Http\Controllers\Api\TrainController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Route;
use Illuminate\Http\Request;
use App\Models\Station;
use App\Models\Train;


class TrainController extends Controller
{
    public function searchTrains(Request $request)
    {
        $request->validate([
            'from_station' => 'required|exists:stations,id',
            'to_station' => 'required|exists:stations,id',
            'departure_time' => 'required|date_format:Y-m-d H:i:s',
        ]);

        $fromStation = $request->input('from_station');
        $fromStationId = Station::where('name', $fromStation)->first()->id;
        $toStation = $request->input('to_station');
        $toStationId = Station::where('name', $toStation)->first()->id;
        $departureTime = $request->input('departure_time');

        // Query to find trains that satisfy the criteria
        $trains = Train::whereHas('routes', function ($query) use ($fromStationId) {
            $query->where('station_id', $fromStationId);
        })
        ->whereHas('routes', function ($query) use ($toStationId) {
            $query->where('station_id', $toStationId);
        })
        ->with(['routes' => function ($query) use ($fromStationId, $toStationId) {
            $query->whereIn('station_id', [$fromStationId, $toStationId])
                  ->orderBy('sequence_number');
        }])
        ->get()
        ->filter(function ($train) use ($fromStationId, $toStationId) {
            $from = $train->routes->firstWhere('station_id', $fromStationId);
            $to   = $train->routes->firstWhere('station_id', $toStationId);

            return $from && $to && $from->sequence_number < $to->sequence_number;
        });

        return response()->json(['trains' => $trains]);
    }
}