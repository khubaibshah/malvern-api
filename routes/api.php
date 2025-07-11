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
use App\Http\Controllers\ScsCarController;
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
Route::post('/customerbookings', [CustomerBookingController::class, 'store']);

//will have to move this one to protected routes because it is admin panel needs api key to send request




// Protected routes
Route::group(['middleware' => ['auth:sanctum']], function(){
    
    //vehicle data
    Route::post('/upload-scs-car', [ScsCarController::class, 'store']);
    Route::put('/update-car/{id}', [ScsCarController::class, 'put']);
    Route::post('/scs-car-images', [ScsCarImageController::class, 'store']);


    Route::get('/scs-car-images/{id}', [ScsCarImageController::class, 'show']);
    Route::get('/scs-car-images', [ScsCarImageController::class, 'getAllCarsImages']);
    Route::get('/scs-cars', [ScsCarImageController::class, 'getAllCars']);
    Route::get('/get-vehicle-by-id/{vehicleId}', [ScsCarController::class, 'get']);
    Route::get('/dvsa-vehicle-details/{registrationNumber}', [DVSAVehicleController::class, 'getMOTTests']);
    Route::get('/get-vehicle-details/{registration}', [VehicleDetailsController::class, 'VesVehicleDetails']);
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::get('/admin-bookings', [AdminBookingController::class, 'index']);
    Route::post('/admin-bookings', [AdminBookingController::class, 'store']);
    Route::get('/users', [UserController::class, 'index']);
});

