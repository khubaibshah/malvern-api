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
Route::get('/vehicle-list', [ScsCarImageController::class, 'getAllCars']);
Route::get('/customer-booking', [CustomerBookingController::class, 'index']);
Route::post('/customer-booking', [CustomerBookingController::class, 'store']);
Route::get('/customer-job-category', [JobCategoryController::class, 'index']);
Route::get('/customer-job-sub-categories', [JobSubCategoryController::class, 'index']);
Route::get('/customer-allCategories', [JobCategoryController::class, 'getJobCategoriesWithSubcategories']);


//crud for vehicle

Route::post('/vehicle-upload', [ScsCarController::class, 'store']);
Route::put('/update-car/{id}', [ScsCarController::class, 'put']);
Route::get('/test-s3', function() {
    try {
        $s3 = new \Aws\S3\S3Client([
            'version' => 'latest',
            'region'  => env('AWS_DEFAULT_REGION'),
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ]
        ]);

        // Test listing buckets
        $buckets = $s3->listBuckets();
        
        // Test uploading a small file
        $result = $s3->putObject([
            'Bucket' => env('AWS_BUCKET'),
            'Key'    => 'test-file.txt',
            'Body'   => 'Hello from Railway!'
        ]);

        return response()->json([
            'success' => true,
            'buckets' => array_column($buckets['Buckets'], 'Name'),
            'upload' => $result->toArray()
        ]);
    } catch (\Exception $e) {
        Log::error('S3 Test Failed', ['error' => $e->getMessage()]);
        return response()->json([
            'error' => $e->getMessage(),
            'env' => [
                'region' => env('AWS_DEFAULT_REGION'),
                'bucket' => env('AWS_BUCKET'),
                'key' => env('AWS_ACCESS_KEY_ID') ? '***'.substr(env('AWS_ACCESS_KEY_ID'), -4) : null
            ]
        ], 500);
    }
});