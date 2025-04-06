<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Discount;

class DiscountController extends Controller
{
    public function index()
    {
        return response()->json(Discount::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nazov' => 'required|string|max:255',
            'vyska' => 'required|numeric|min:0|max:100',
        ]);

        $discount = Discount::create($data);

        return response()->json(['message' => 'Zľava vytvorená', 'data' => $discount], 201);
    }

    public function show($id)
    {
        $discount = Discount::findOrFail($id);
        return response()->json($discount);
    }

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

    public function destroy($id)
    {
        $discount = Discount::findOrFail($id);
        $discount->delete();

        return response()->json(['message' => 'Zľava bola vymazaná']);
    }
}
