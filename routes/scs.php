<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DVSAVehicleController;


# get vehicles to display
Route::get('/vehicle', [ScsVehicleController::class, 'getVehicles']);
Route::get('/vehicles/{vehicleId}', [ScsVehicleController::class, 'getById']);
Route::get('/vehicle/advanced-filters', [ScsVehicleController::class, 'advancedFilters']);

# emails
Route::post('/lead', [EmailController::class, 'lead']);
Route::post('/lead/sell-your-car', [EmailController::class, 'customerVehicleSale']);
Route::post('/lead/schedule-test-drive', [EmailController::class, 'testDrive']);
Route::get('/get-all-vehicles', [ScsVehicleController::class, 'getAllVehiclesWithImages']);
#dvsa endpoints
Route::get('/dvsa-vehicle-details-scs/{registrationNumber}', [DVSAVehicleController::class, 'getMOTTestsFiltered']);

#autotrader endpoints
Route::get('/at/vehicleList', [AutoTraderVehicleList::class, 'autotraderVehicleList']);
Route::get('/at/{vehicleId}', [AutoTraderVehicleList::class, 'autotraderVehicle']);
Route::get('/at/{registration}/{milage}', [AutoTraderVehicleList::class, 'autotraderValuation']);