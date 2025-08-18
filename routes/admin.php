<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehicleDetailsController;
use App\Http\Controllers\DVSAVehicleController;


Route::group(['middleware' => ['auth:sanctum']], function(){
    

    Route::get('/vehicles', [ScsVehicleController::class, 'getAllVehiclesWithImages']);
    Route::get('/vehicle', [ScsVehicleController::class, 'getVehicles']);
    Route::get('/get-vehicle-by-id/{vehicleId}', [ScsVehicleController::class, 'getById']);
    
    #s3 endpoints
    Route::post('/vehicle-upload', [ScsVehicleController::class, 'store']);
    Route::post('/s3-presigned-url', [ScsVehicleController::class, 'generatePresignedUploadUrl']);
    Route::post('/delete-s3-image', [ScsVehicleController::class, 'deleteS3Image']);
    
    Route::put('/update-car/{id}', [ScsVehicleController::class, 'put']);
    Route::post('/featured-vehicle', [ScsVehicleController::class, 'setFeaturedVehicled']);
    
    Route::post('/archive-vehicles', [ScsVehicleController::class, 'archiveVehicles']);
    Route::get('/archived-vehicles', [ScsVehicleController::class, 'getArchivedVehicles']);
    Route::post('/delete-vehicles', [ScsVehicleController::class, 'deleteVehicles']);
    
    #dvsa endpoints
    Route::post('/ves-auth', [VehicleDetailsController::class, 'authenticateVes']);
    Route::get('/ves/{registration}', [VehicleDetailsController::class, 'VesVehicleDetails']);
    Route::get('/dvsa/{registrationNumber}', [DVSAVehicleController::class, 'getMOTTests']);


    # autotrader endpoints
    Route::get('/at/vehicleList', [AutoTraderVehicleList::class, 'autotraderVehicleList']);
    //hpi report parser
    Route::post('/hpi-report', [HpiReportController::class, 'hpiReport']);

});