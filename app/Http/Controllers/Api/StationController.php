<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Station;
use Illuminate\Http\Request;

class StationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/stations",
     *     summary="Get all stations",
     *     tags={"Stations"},
     *     @OA\Response(
     *         response=200,
     *         description="List of all stations"
     *         
     *     )
     * )
     */
    public function index()
    {
        return response()->json(['stations' => Station::all()]);
    }

    /**
     * @OA\Post(
     *     path="/api/stations/search",
     *     summary="Search for stations by name prefix",
     *     tags={"Stations"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"starts_with"},
     *             @OA\Property(property="starts_with", type="string", example="New")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of matching stations"
     *         
     *     )
     * )
     */
    public function search(Request $request)
    {
        $request->validate([
            'starts_with' => 'required|string|min:3',
        ]);

        $query = $request->input('starts_with');

        $stations = Station::where('name', 'ILIKE', $query . '%')->get();
        
        return response()->json($stations);
    }

    /**
     * @OA\Post(
     *     path="/api/stations",
     *     summary="Create a new station",
     *     tags={"Stations"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Central Station")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Station created"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $station = Station::create($validated);

        return response()->json(['station' => $station], 201);
    }
}
