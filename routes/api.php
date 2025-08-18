<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminBookingController;
use App\Http\Controllers\CustomerBookingController;
use App\Http\Controllers\DVSAVehicleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\VehicleDetailsController;
use App\Http\Controllers\ScsCarImageController;
use App\Http\Controllers\ScsVehicleController;
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

// Protected routes
Route::group(['middleware' => ['auth:sanctum']], function(){
    
    //vehicle data
    Route::post('/upload-scs-car', [ScsVehicleController::class, 'store']);

    Route::put('/update-car/{id}', [ScsVehicleController::class, 'put']);

    Route::get('/get-vehicle-by-id/{vehicleId}', [ScsVehicleController::class, 'get']);

    Route::get('/dvsa-vehicle-details/{registrationNumber}', [DVSAVehicleController::class, 'getMOTTests']);

    Route::get('/get-vehicle-details/{registration}', [VehicleDetailsController::class, 'VesVehicleDetails']);

    Route::post('/logout', [LoginController::class, 'logout']);

    Route::get('/users', [UserController::class, 'index']);
});

