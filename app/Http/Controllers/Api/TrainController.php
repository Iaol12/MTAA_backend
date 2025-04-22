<?php
// filepath: d:\mta\MTAA_backend\app\Http\Controllers\Api\TrainController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Route;
use Illuminate\Http\Request;
use App\Models\Station;
use App\Models\Train;
use Illuminate\Support\Facades\Log;




class TrainController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/trains",
     *     summary="Create a new train with routes",
     *     tags={"Trains"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "routes"},
     *             @OA\Property(property="name", type="string", example="Express Train", description="Name of the train"),
     *             @OA\Property(
     *                 property="routes",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"station_id", "sequence_number", "departure_time"},
     *                     @OA\Property(property="station_id", type="integer", example=1, description="ID of the station"),
     *                     @OA\Property(property="sequence_number", type="integer", example=1, description="Order of the station in the route"),
     *                     @OA\Property(property="departure_time", type="string", format="date-time", example="2025-04-15T08:00:00Z", description="Departure time in ISO 8601 format")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Train created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        // print for testing
        Log::info('Incoming request:', $request->all());

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'routes' => 'required|array|min:1',
            'routes.*.station_id' => 'required|exists:stations,id',
            'routes.*.sequence_number' => 'required|integer|min:1',
            'routes.*.departure_time' => 'required|date_format:Y-m-d H:i:s',
        ]);

        $validated['schedule'] = 1;

        // Create the train
        $train = Train::create([
    'name' => $validated['name'],
    'schedule' => $validated['schedule'],
]);

        // Create the routes
        foreach ($validated['routes'] as $route) {
            Route::create([
                'train_id' => $train->id,
                'station_id' => $route['station_id'],
                'sequence_number' => $route['sequence_number'],
                'departure_time' => $route['departure_time'],
            ]);
        }

        return response()->json([
            'train' => $train,
            'routes' => $train->routes,
        ], 201);
    }
    /**
     * @OA\Post(
     *     path="/api/trains/search",
     *     summary="Search for trains between two stations",
     *     tags={"Trains"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"from_station", "to_station", "departure_time"},
     *             @OA\Property(property="from_station", type="string", example="Central Station", description="Name of the departure station"),
     *             @OA\Property(property="to_station", type="string", example="North Station", description="Name of the destination station"),
     *             @OA\Property(property="departure_time", type="string", format="date-time", example="2025-04-15 08:00:00", description="Desired departure time in 'Y-m-d H:i:s' format")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of trains matching the search criteria",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="trains",
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
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