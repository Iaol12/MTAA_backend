<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
use App\Models\Item;

Route::get('/items', function () {
    return response()->json(Item::all());
});

Route::post('/items', function (Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
    ]);
    
    $item = Item::create(['name' => $request->name]);
    return response()->json($item, 201);
});
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
