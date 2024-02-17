<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserAuthController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//  Public routes
Route::post('/register',[LoginController::class,'register']);
Route::post('/login', [LoginController::class, 'login']);
// Route::post('/customerbookings', [BookingController::class, 'store']);

// Protected routes
Route::group(['middleware' => ['auth:sanctum']], function(){
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/users', [UserController::class, 'index']);
});

