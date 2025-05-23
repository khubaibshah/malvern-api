<?php

namespace App\Http\Controllers;

use App\Models\ScsCar;
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

    //update vehicle data and images
    public function put(Request $request, int $id): JsonResponse
    {
        $result = $this->vehicleService->updateVehicleWithImages($request, $id);

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['status']);
        }

        if (isset($result['errors'])) {
            return response()->json(['errors' => $result['errors']], $result['status']);
        }

        return response()->json(['message' => $result['message'], 'car' => $result['car']], $result['status']);
    }

    //get vehicle data and images from s3
    public function get($vehicleId): JsonResponse
    {
        $result = $this->vehicleService->getVehicleWithImages($vehicleId);

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['status']);
        }

        if (isset($result['errors'])) {
            return response()->json(['errors' => $result['errors']], $result['status']);
        }

        return response()->json(['message' => $result['message'], 'car' => $result['car']], $result['status']);
    }
}
