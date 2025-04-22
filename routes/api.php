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
use App\Http\Controllers\Api\TrainController;
use App\Http\Controllers\ImageController;

//trains
Route::get('/trains/search', [TrainController::class, 'searchTrains']);
Route::post('/trains', [TrainController::class, 'store']);

//stations
Route::post('/stations', [StationController::class, 'store']);
Route::get('/stations', [StationController::class, 'index']);
Route::post('/stations/search', [StationController::class, 'search']);


//user
Route::get('/users', [UserController::class, 'index']);
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::middleware('auth:sanctum')->get('/user', [UserController::class, 'getUser']);
Route::middleware('auth:sanctum')->put('/user/profile', [UserController::class, 'updateProfile']);


//discounts
Route::apiResource('zlavy', DiscountController::class); // vygeneruje všetky CRUD operácie
Route::get('/zlavy', [DiscountController::class, 'index']);     //v podstate tieto    
Route::get('/zlavy/{id}', [DiscountController::class, 'show']);    // <-
Route::post('/zlavy', [DiscountController::class, 'store']);       
Route::put('/zlavy/{id}', [DiscountController::class, 'update']);  
Route::delete('/zlavy/{id}', [DiscountController::class, 'destroy']); 

//discountCard
Route::apiResource('karty', DiscountCardController::class);

//user Roles
Route::apiResource('roles', UserRoleController::class);

//post image
Route::post('/upload', [ImageController::class, 'store']);

//get image
Route::get('/image/{filename}', [ImageController::class, 'show']);
