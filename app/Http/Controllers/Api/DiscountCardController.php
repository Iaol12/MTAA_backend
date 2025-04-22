<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DiscountCard;
use Illuminate\Http\Request;

class DiscountCardController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/discount-cards",
     *     summary="Get all discount cards",
     *     tags={"Discount Cards"},
     *     @OA\Response(
     *         response=200,
     *         description="List of discount cards",
     *         
     *     )
     * )
     */
    public function index()
    {
        return response()->json(DiscountCard::with('discount')->get());
    }

    /**
     * @OA\Post(
     *     path="/api/discount-cards",
     *     summary="Create a new discount card",
     *     tags={"Discount Cards"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"zlava_id", "zlavovy_kod"},
     *             @OA\Property(property="zlava_id", type="integer", example=1),
     *             @OA\Property(property="zlavovy_kod", type="string", example="DISCOUNT2025")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Discount card created",
     *         
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'zlava_id' => 'required|exists:discounts,id',
            'zlavovy_kod' => 'required|string|unique:discount_cards,zlavovy_kod|max:255',
        ]);

        $card = DiscountCard::create($validated);

        return response()->json($card, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/discount-cards/{id}",
     *     summary="Get a specific discount card",
     *     tags={"Discount Cards"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the discount card"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Discount card details",
     *         
     *     )
     * )
     */
    public function show($id)
    {
        $card = DiscountCard::with('discount')->findOrFail($id);
        return response()->json($card);
    }

    /**
     * @OA\Put(
     *     path="/api/discount-cards/{id}",
     *     summary="Update a discount card",
     *     tags={"Discount Cards"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the discount card"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="zlava_id", type="integer", example=1),
     *             @OA\Property(property="zlavovy_kod", type="string", example="DISCOUNT2025")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Discount card updated",
     *         
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $card = DiscountCard::findOrFail($id);

        $validated = $request->validate([
            'zlava_id' => 'sometimes|exists:discounts,id',
            'zlavovy_kod' => 'sometimes|string|max:255|unique:discount_cards,zlavovy_kod,' . $id,
        ]);

        $card->update($validated);

        return response()->json($card);
    }

    /**
     * @OA\Delete(
     *     path="/api/discount-cards/{id}",
     *     summary="Delete a discount card",
     *     tags={"Discount Cards"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the discount card"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Discount card deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Zľavová karta bola vymazaná.")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $card = DiscountCard::findOrFail($id);
        $card->delete();

        return response()->json(['message' => 'Zľavová karta bola vymazaná.']);
    }
}