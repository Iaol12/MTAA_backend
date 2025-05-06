<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TicketController extends Controller
{
    /**
     * Get all tickets of the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    
    public function getUserTickets()
    {
        Carbon::setLocale('sk');
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $tickets = Ticket::where('user_id', $user->id)
        ->with(['train.routes', 'startStation', 'endStation'])
        ->get()->map(function ($ticket) {
            // Find the route for the start station
            $startRoute = $ticket->train->routes->firstWhere('station_id', $ticket->startStation->id);
            $endRoute = $ticket->train->routes->firstWhere('station_id', $ticket->endStation->id);

            $ticket->departure_time_at = $startRoute
                ? Carbon::parse($startRoute->departure_time)->isoFormat('D.M. H:mm')
                : null;

            $ticket->arrival_time_at = $endRoute
                ? Carbon::parse($endRoute->departure_time)->isoFormat('D.M. H:mm')
                : null;

            return $ticket;
        });

        return response()->json(['tickets' => $tickets]);
    }
}
