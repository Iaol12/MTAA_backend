<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Station;
use Illuminate\Http\Request;

class StationController extends Controller
{

    public function search(Request $request)
    {
        $request->validate([
            'starts_with' => 'required|string|min:3',
        ]);

        $query = $request->input('starts_with');

        $stations = Station::where('name', 'ILIKE', $query . '%')->get();
        
        return response()->json($stations);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $station = Station::create($validated);

        return response()->json(['station' => $station], 201);
    }

    public function index()
    {
        return response()->json(['stations' => Station::all()]);
    }
}
