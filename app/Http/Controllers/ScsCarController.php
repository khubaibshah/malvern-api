<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Services\VehicleService;

class ScsCarController extends Controller
{
    public function __construct(protected VehicleService $vehicleService) {}

    public function store(Request $request): JsonResponse
    {
        $result = $this->vehicleService->createVehicleWithImages($request);

        if (isset($result['errors'])) {
            return response()->json(['errors' => $result['errors']], $result['status']);
        }

        return response()->json([
            'message' => 'Car and images successfully created and uploaded',
            'car' => $result['car']
        ], $result['status']);
    }
}
