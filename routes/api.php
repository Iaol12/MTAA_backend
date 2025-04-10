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

use App\Http\Controllers\Api\StationController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\DiscountController;
use App\Http\Controllers\Api\DiscountCardController;
use App\Http\Controllers\Api\UserRoleController;


//stations
Route::post('/stations', [StationController::class, 'store']);
Route::get('/stations', [StationController::class, 'index']);
Route::post('/stations/search', [StationController::class, 'search']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//user
Route::get('/users', [UserController::class, 'index']);
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::middleware('auth:sanctum')->get('/user', [UserController::class, 'getUser']);

//discounts
Route::apiResource('zlavy', DiscountController::class); // vygeneruje všetky CRUD operácie
Route::get('/zlavy', [DiscountController::class, 'index']);        
Route::get('/zlavy/{id}', [DiscountController::class, 'show']);    
Route::post('/zlavy', [DiscountController::class, 'store']);       
Route::put('/zlavy/{id}', [DiscountController::class, 'update']);  
Route::delete('/zlavy/{id}', [DiscountController::class, 'destroy']); 

//discountCard
Route::apiResource('karty', DiscountCardController::class);

//user Roles
Route::apiResource('roles', DiscountCardController::class);
