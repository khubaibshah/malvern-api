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

    public function autotraderVehicleList()
    {
        SyncAutoTraderVehiclesJob::dispatch();

        return response()->json([
            'message' => 'AutoTrader vehicle sync job has been dispatched.',
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
}
