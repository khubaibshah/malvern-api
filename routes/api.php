<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminBookingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\VehicleDetailsController;
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
Route::post('/vehicle-details', [VehicleDetailsController::class, 'getVehicleDetails']);
Route::post('/customerbookings', [BookingController::class, 'store']);

// Protected routes
Route::group(['middleware' => ['auth:sanctum']], function(){
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::get('/admin-bookings', [AdminBookingController::class, 'index']);
    Route::post('/admin-bookings', [AdminBookingController::class, 'store']);
    Route::get('/users', [UserController::class, 'index']);
});

