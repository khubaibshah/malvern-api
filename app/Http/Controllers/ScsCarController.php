<?php

namespace App\Http\Controllers;

use App\Models\ScsCar;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Services\VehicleService;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Log;

class ScsCarController extends Controller
{
    public function __construct(protected VehicleService $vehicleService) {}

    public function store(Request $request): JsonResponse
    {
        Log::info('ScsCarController@store hit');
        Log::debug('VehicleService::__construct hit test');
        $result = $this->vehicleService->createVehicleWithImages($request);
        Log::debug('VehicleService result', $result);

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

    //get vehicle data and images from s3 using vehicle id one vehicle 
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

    public function getAllVehiclesWithImages(): JsonResponse
    {
        $result = $this->vehicleService->getAll();

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['status']);
        }

        return response()->json(['cars' => $result['cars']], $result['status']);
    }

    //for front end
    public function advancedFilters(): JsonResponse {}

    public function featuredVehicled(): JsonResponse {}

    
    public function generatePresignedUploadUrl(Request $request)
    {
        $s3 = new S3Client([
            'region' => 'your-region',
            'version' => 'latest',
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ]
        ]);

        $key = 'car_images/' . uniqid() . '_' . $request->input('filename');
        $bucket = env('AWS_BUCKET');

        $cmd = $s3->getCommand('PutObject', [
            'Bucket' => $bucket,
            'Key'    => $key,
            'ContentType' => $request->input('contentType'),
            'ACL' => 'public-read',
        ]);

        $request = $s3->createPresignedRequest($cmd, '+5 minutes');

        return response()->json([
            'url' => (string) $request->getUri(),
            'key' => $key,
        ]);
    }
}
