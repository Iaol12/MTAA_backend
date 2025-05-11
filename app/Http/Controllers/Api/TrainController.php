<?php
// filepath: d:\mta\MTAA_backend\app\Http\Controllers\Api\TrainController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Route;
use Illuminate\Http\Request;
use App\Models\Station;
use App\Models\Train;
use Illuminate\Support\Facades\Log;
use App\Services\ExpoPushNotificationService;
use App\Models\User;

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
            'discounted_tickets' => 20,
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


    public function sendDelayNotification($trainId)
    {
        // Skontroluj, či je používateľ admin
        if(auth()->user()->privilege != 2) {
            return response()->json(['error' => 'Nemáte práva na odosielanie notifikácií.'], 403);
        }

        // Získaj všetkých používateľov s expo_token
        $users = User::whereNotNull('expo_token')->get(); // môžeš filtrovať podľa rôznych kritérií

        foreach ($users as $user) {
            // Zavolaj ExpoPushNotificationService na odoslanie notifikácie
            app(ExpoPushNotificationService::class)->sendPushNotification(
                $user->expo_token,
                'Notifikácia o meškaní',
                'Vlak je oneskorený, prosím, počkajte na ďalšie informácie.'
            );
        }

        return response()->json(['message' => 'Notifikácie odoslané.']);
    }

    public function delay(Request $request, Train $train)
    {
        // Check if the authenticated user is an admin
        if(auth()->user()->role->privilege != 2) {
            return response()->json(['error' => 'Nemáte oprávnenie na nastavenie meškania vlaku.'], 403);
        }

        $request->validate([
            'hours' => 'nullable|integer|min:0',
            'minutes' => 'required|integer|min:0',
        ]);

        // Získanie hodín a minút z requestu
        $hours = $request->input('hours', 0); // Ak sú hodiny nevyplnené, predpokladáme 0
        $minutes = $request->input('minutes');

        // Spočítanie meškania v minútach
        $delayMinutes = ($hours * 60) + $minutes;

        // Ak používame typ time, prevod na správny formát
        $delayTime = sprintf('%02d:%02d', floor($delayMinutes / 60), $delayMinutes % 60);

        // Uloženie meškania ako čas
        $train->delay = $delayTime;  // Predpokladáme, že delay je typu 'time'
        $train->save();

        // Poslať push notifikácie všetkým používateľom s expo tokenom
        $users = User::whereNotNull('expo_token')->get();

        // Get service instance once outside the loop
        $notificationService = app(\App\Services\ExpoPushNotificationService::class);

        foreach ($users as $user) {
            try {
                // Skip users with invalid tokens
                if (empty($user->expo_token)) {
                    \Log::warning("Preskakujem používateľa {$user->id} - chýbajúci expo token");
                    continue;
                }
                
                $notificationService->sendPushNotification(
                    $user->expo_token,
                    'Meškanie vlaku',
                    "{$train->name} mešká {$delayMinutes} minút."
                );
                
                \Log::info("Odosielam push pre používateľa {$user->id} na token {$user->expo_token}");
            } catch (\Exception $e) {
                // Log the error but continue with other users
                \Log::error("Chyba pri odosielaní push notifikácie používateľovi {$user->id}: {$e->getMessage()}");
            }
        }

        return response()->json(['message' => 'Meškanie bolo úspešne odoslané.']);
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
     *             @OA\Property(property="departure_time", type="string", format="date-time", example="2025-04-15 08:00:00", description="Desired departure time in 'Y-m-d H:i:s' format"),
     *             @OA\Property(property="page", type="integer", example=1, description="Page number for pagination"),
     *             @OA\Property(property="per_page", type="integer", example=15, description="Number of trains per page")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of trains matching the search criteria",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="trains",
     *             ),
     *             @OA\Property(
     *                 property="pagination",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer", example=100, description="Total number of trains"),
     *                 @OA\Property(property="per_page", type="integer", example=15, description="Number of trains per page"),
     *                 @OA\Property(property="current_page", type="integer", example=1, description="Current page number"),
     *                 @OA\Property(property="last_page", type="integer", example=7, description="Last page number")
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
            'page' => 'integer|min:1',
            'per_page' => 'integer|min:1|max:50',
        ]);

        $fromStationId = $request->input('from_station');
        $toStationId = $request->input('to_station');
        $departureTime = $request->input('departure_time');
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 15); // Default to 15 trains per page

        // Query to find trains that satisfy the criteria
        $trains = Train::whereHas('routes', function ($query) use ($fromStationId, $departureTime) {
            $query->where('station_id', $fromStationId)
                  ->where('departure_time', '>=', $departureTime);
        })
        ->whereHas('routes', function ($query) use ($toStationId) {
            $query->where('station_id', $toStationId);
        })
        ->with('routes.station')
        ->get()
        ->filter(function ($train) use ($fromStationId, $toStationId) {
            $from = $train->routes->firstWhere('station_id', $fromStationId);
            $to   = $train->routes->firstWhere('station_id', $toStationId);

            return $from && $to && $from->sequence_number < $to->sequence_number;
        });

        // Filter routes to include only those between from_station and to_station
        $trains = $trains->map(function ($train) use ($fromStationId, $toStationId) {
            $fromSequence = $train->routes->firstWhere('station_id', $fromStationId)->sequence_number;
            $toSequence = $train->routes->firstWhere('station_id', $toStationId)->sequence_number;

            $filteredRoutes = $train->routes->filter(function ($route) use ($fromSequence, $toSequence) {
                return $route->sequence_number >= $fromSequence && $route->sequence_number <= $toSequence;
            })->map(function ($route) {
                return [
                    'station_id' => $route->station_id,
                    'station_name' => $route->station->name,
                    'sequence_number' => $route->sequence_number,
                    'departure_time' => $route->departure_time,
                ];
            })->values();

            // Override the routes relationship with the filtered collection
            $train->setRelation('routes', $filteredRoutes);

            if ($train->delay) {
                list($hours, $minutes) = array_pad(explode(':', $train->delay), 2, 0);
                $train->delay = (int)$hours * 60 + (int)$minutes;
                
            }

            return $train;
        });
        $trains = $trains->sortBy(function ($train) {
            // Get the first route in sequence (departure station)
            $firstRoute = $train->routes->sortBy('sequence_number')->first();
            return $firstRoute ? $firstRoute['departure_time'] : null;
        })->values();

        // Apply pagination manually
        $total = $trains->count();
        $trainsPaginated = $trains->forPage($page, $perPage)->values();

        return response()->json([
            'trains' => $trainsPaginated,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => ceil($total / $perPage),
            ]
        ]);
    }
}