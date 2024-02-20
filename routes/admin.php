<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\VehicleDetailsController;
use App\Http\Controllers\InfoController;

Route::post('/customerbookings', [BookingController::class, 'store']);
Route::get('/customerbookings', [BookingController::class, 'index']);
// Route::post('/vehicle-details', [VehicleDetailsController::class, 'getVehicleDetails']);
Route::post('/get-vehicle-details', [VehicleDetailsController::class, 'VesVehicleDetails']);
Route::get('/vehicle-details', [VehicleController::class, 'index']);
Route::post('/vehicle-details', [VehicleController::class, 'store']);

