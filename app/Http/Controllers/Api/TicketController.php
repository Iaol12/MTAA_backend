<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /**
     * Get all tickets of the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserTickets()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $tickets = Ticket::where('user_id', $user->id)
        ->with(['train.routes', 'startStation', 'endStation'])
            ->orderBy('created_at', 'desc')
        ->get();

        return response()->json(['tickets' => $tickets]);
    }
}
