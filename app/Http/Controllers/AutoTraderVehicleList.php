<?php

namespace App\Http\Controllers;

use App\Jobs\SyncAutoTraderVehiclesJob;
use App\Services\AutoTraderService;
use Illuminate\Http\Request;

class AutoTraderVehicleList extends Controller
{
    protected AutoTraderService $autoTraderService;

    public function __construct(AutoTraderService $autoTraderService)
    {
        $this->autoTraderService = $autoTraderService;
    }

    public function autotraderVehicleList(AutoTraderService $autoTraderService)
    {
        $vehicles = $autoTraderService->getVehicleList();

        return response()->json([
            'message' => 'Vehicles saved. Image jobs dispatched in background.',
            'data' => $vehicles,
        ]);
    }

    public function autotraderVehicle(Request $request)
    {
        $vehicleId = $request->route('vehicleId');
        $data = $this->autoTraderService->getVehicle(vehicleId: $vehicleId);

        return response()->json([
            'message' => 'AutoTrader vehicle fetched.',
            'data' => $data,
        ]);
    }

    public function autotraderValuation(Request $request)
    {
        $registration = $request->route('registration');
        $milage = $request->route('milage');
        $data = $this->autoTraderService->getValuation($registration, $milage);

        return response()->json([
            'message' => 'AutoTrader valuation fetched.',
            'data' => $data,
        ]);
    }
}
