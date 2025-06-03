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
use Illuminate\Support\Facades\Log;

// Route::post('/vehicle-details', [VehicleDetailsController::class, 'getVehicleDetails']);
Route::get('/get-vehicle-details/{registration}', [VehicleDetailsController::class, 'VesVehicleDetails']);
Route::post('/ves-auth', [VehicleDetailsController::class, 'authenticateVes']);
Route::get('/vehicle-details', [VehicleController::class, 'index']);
Route::post('/vehicle-details', [VehicleController::class, 'store']);
Route::get('/vehicle-details/{registrationNumber}', [VehicleController::class, 'show']);
Route::get('/dvsa-vehicle-details/{registrationNumber}', [DVSAVehicleController::class, 'getMOTTests']);
Route::get('/vehicle-list', [ScsCarImageController::class, 'getAllCars']);
Route::get('/customer-booking', [CustomerBookingController::class, 'index']);
Route::post('/customer-booking', [CustomerBookingController::class, 'store']);
Route::get('/customer-job-category', [JobCategoryController::class, 'index']);
Route::get('/customer-job-sub-categories', [JobSubCategoryController::class, 'index']);
Route::get('/customer-allCategories', [JobCategoryController::class, 'getJobCategoriesWithSubcategories']);


//crud for vehicle

Route::post('/vehicle-upload', [ScsCarController::class, 'store']);
Route::post('/s3-presigned-url', [ScsCarController::class, 'generatePresignedUploadUrl']);
Route::post('/delete-s3-image', [ScsCarController::class, 'deleteS3Image']);

Route::put('/update-car/{id}', [ScsCarController::class, 'put']);
Route::post('/featured-vehicle', [ScsCarController::class, 'featuredVehicled']);
Route::get('/get-all-vehicles', [ScsCarController::class, 'getAllVehiclesWithImages']);
