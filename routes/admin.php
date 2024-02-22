<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\VehicleDetailsController;
use App\Http\Controllers\InfoController;
use App\Http\Controllers\CustomerBookingController;
use App\Http\Controllers\JobCategoryController;

// Route::post('/vehicle-details', [VehicleDetailsController::class, 'getVehicleDetails']);
Route::post('/get-vehicle-details', [VehicleDetailsController::class, 'VesVehicleDetails']);
Route::get('/vehicle-details', [VehicleController::class, 'index']);
Route::post('/vehicle-details', [VehicleController::class, 'store']);
Route::get('/vehicle-details/{registrationNumber}', [VehicleController::class, 'show']);
Route::get('/customer-booking', [CustomerBookingController::class, 'index']);
Route::get('/customer-job-categories', [JobCategoryController::class, 'index']);


