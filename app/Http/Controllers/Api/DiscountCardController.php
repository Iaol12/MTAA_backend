<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DiscountCard;
use Illuminate\Http\Request;

class DiscountCardController extends Controller
{
    public function index()
    {
        return response()->json(DiscountCard::with('discount')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'zlava_id' => 'required|exists:discounts,id',
            'zlavovy_kod' => 'required|string|unique:discount_cards,zlavovy_kod|max:255',
        ]);

        $card = DiscountCard::create($validated);

        return response()->json($card, 201);
    }

    public function show($id)
    {
        $card = DiscountCard::with('discount')->findOrFail($id);
        return response()->json($card);
    }

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

    public function destroy($id)
    {
        $card = DiscountCard::findOrFail($id);
        $card->delete();

        return response()->json(['message' => 'Zľavová karta bola vymazaná.']);
    }
}