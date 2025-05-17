<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\VehicleDetailsController;
use App\Http\Controllers\DVSAVehicleController;
use App\Http\Controllers\InfoController;
use App\Http\Controllers\CustomerBookingController;
use App\Http\Controllers\JobCategoryController;
use App\Http\Controllers\JobSubCategoryController;

// Route::post('/vehicle-details', [VehicleDetailsController::class, 'getVehicleDetails']);
Route::get('/get-vehicle-details/{registration}', [VehicleDetailsController::class, 'VesVehicleDetails']);
Route::post('/ves-auth', [VehicleDetailsController::class, 'authenticateVes']);
Route::get('/vehicle-details', [VehicleController::class, 'index']);
Route::post('/vehicle-details', [VehicleController::class, 'store']);
Route::get('/vehicle-details/{registrationNumber}', [VehicleController::class, 'show']);
Route::get('/dvsa-vehicle-details/{registrationNumber}', [DVSAVehicleController::class, 'getMOTTests']);
Route::get('/customer-booking', [CustomerBookingController::class, 'index']);
Route::post('/customer-booking', [CustomerBookingController::class, 'store']);
Route::get('/customer-job-category', [JobCategoryController::class, 'index']);
Route::get('/customer-job-sub-categories', [JobSubCategoryController::class, 'index']);
Route::get('/customer-allCategories', [JobCategoryController::class, 'getJobCategoriesWithSubcategories']);


