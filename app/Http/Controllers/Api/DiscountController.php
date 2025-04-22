<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Discount;

class DiscountController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/discounts",
     *     summary="Get all discounts",
     *     tags={"Discounts"},
     *     @OA\Response(
     *         response=200,
     *         description="List of discounts",
     *         
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Discount::all());
    }

    /**
     * @OA\Post(
     *     path="/api/discounts",
     *     summary="Create a new discount",
     *     tags={"Discounts"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nazov", "vyska"},
     *             @OA\Property(property="nazov", type="string", example="Summer Sale"),
     *             @OA\Property(property="vyska", type="number", format="float", example=20.5)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Discount created",
     *         
     *     )
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nazov' => 'required|string|max:255',
            'vyska' => 'required|numeric|min:0|max:100',
        ]);

        $discount = Discount::create($data);

        return response()->json(['message' => 'Zľava vytvorená', 'data' => $discount], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/discounts/{id}",
     *     summary="Get a specific discount",
     *     tags={"Discounts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the discount"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Discount details",
     *         
     *     )
     * )
     */
    public function show($id)
    {
        $discount = Discount::findOrFail($id);
        return response()->json($discount);
    }

    /**
     * @OA\Put(
     *     path="/api/discounts/{id}",
     *     summary="Update a discount",
     *     tags={"Discounts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the discount"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nazov", type="string", example="Winter Sale"),
     *             @OA\Property(property="vyska", type="number", format="float", example=15.0)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Discount updated",
     *         
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $discount = Discount::findOrFail($id);

        $data = $request->validate([
            'nazov' => 'sometimes|string|max:255',
            'vyska' => 'sometimes|numeric|min:0|max:100',
        ]);

        $discount->update($data);

        return response()->json(['message' => 'Zľava aktualizovaná', 'data' => $discount]);
    }

    /**
     * @OA\Delete(
     *     path="/api/discounts/{id}",
     *     summary="Delete a discount",
     *     tags={"Discounts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the discount"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Discount deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Zľava bola vymazaná")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $discount = Discount::findOrFail($id);
        $discount->delete();

        return response()->json(['message' => 'Zľava bola vymazaná']);
    }
}
