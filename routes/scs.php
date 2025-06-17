<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehicleDetailsController;
use App\Http\Controllers\DVSAVehicleController;
use Illuminate\Support\Facades\Http;

// Route::post('/vehicle-details', [VehicleDetailsController::class, 'getVehicleDetails']);
Route::post('/ves-auth', [VehicleDetailsController::class, 'authenticateVes']);
Route::get('/vehicle-details', [VehicleController::class, 'index']);
Route::post('/vehicle-details', [VehicleController::class, 'store']);
Route::get('/vehicle-details/{registrationNumber}', [VehicleController::class, 'show']);
Route::get('/dvsa-vehicle-details/{registrationNumber}', [DVSAVehicleController::class, 'getMOTTests']);
Route::get('/vehicle-list', [ScsCarImageController::class, 'getAllCars']);



//get vehicles to display 
Route::get('/get-vehicle-by-id/{vehicleId}', [ScsCarController::class, 'get']);
Route::get('/get-all-vehicles', [ScsCarController::class, 'getAllVehiclesWithImages']);
Route::get('/advanced-filters', [ScsCarController::class, 'advancedFilters']);

# emails
Route::post('/lead', [EmailController::class, 'lead']);
Route::post('/schedule-test-drive', [EmailController::class, 'testDrive']);
Route::post('/sell-your-car', [EmailController::class, 'customerVehicleSale']);


#dvsa endpoints
Route::get('/get-vehicle-details/{registration}', [VehicleDetailsController::class, 'VesVehicleDetails']);
Route::get('/dvsa-vehicle-details-scs/{registrationNumber}', [DVSAVehicleController::class, 'getMOTTestsFiltered']);


#google api
// Route::get('/google-api', function () {
//     $response = Http::get('https://maps.googleapis.com/maps/api/place/details/json', [
//         // 'address' => '1600 Amphitheatre Parkway, Mountain View, CA',
//         'placeid' => config('services.google.place_id'),
//         'key' => config('services.google.api_key'),
//     ]); 
//     return response()->json($response->json());
// });

