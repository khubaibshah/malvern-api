<?php

namespace App\Http\Controllers;

use App\Models\ScsCar;
use App\Services\AwsS3Service;
use App\Services\LeadService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Services\VehicleService;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

        if (isset($result['car'])) {
            return response()->json([
                'message' => 'Car and images successfully created and uploaded',
                'car' => $result['car']
            ], $result['status']);
        }

        // fallback for unexpected structure
        return response()->json([
            'error' => $result['error'] ?? 'Unknown error occurred.',
            'message' => $result['message'] ?? '',
        ], $result['status'] ?? 500);
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
    // public function advancedFilters(): JsonResponse {}

    public function setFeaturedVehicled(Request $request): JsonResponse
    {
        $vehicleId = $request->input('id');

        // Reset all vehicles
        ScsCar::where('featured', 1)->update(['featured' => 0]);

        // Set the new featured one
        ScsCar::where('id', $vehicleId)->update(['featured' => 1]);

        return response()->json(['message' => 'Featured vehicle updated.']);
    }



    public function generatePresignedUploadUrl(Request $request)
    {
        $s3 = new S3Client([
            'region' => config('filesystems.disks.s3.region'),
            'version' => 'latest',
            'credentials' => [
                'key' => config('filesystems.disks.s3.key'),
                'secret' => config('filesystems.disks.s3.secret'),
            ]
        ]);

        $registration = strtoupper(preg_replace('/[^A-Z0-9]/', '', $request->input('registration', 'unknown')));
        $filename = uniqid() . '_' . $request->input('filename');
        $key = "car_images/{$registration}/{$filename}";
        $bucket = config('filesystems.disks.s3.bucket');

        $cmd = $s3->getCommand('PutObject', [
            'Bucket' => $bucket,
            'Key' => $key,
            'ContentType' => $request->input('contentType'),
            'ACL' => 'public-read',
        ]);

        $presignedRequest = $s3->createPresignedRequest($cmd, '+5 minutes');

        return response()->json([
            'url' => (string) $presignedRequest->getUri(),
            'key' => $key,
        ]);
    }


    public function deleteS3Image(Request $request): JsonResponse
    {
        $imageUrl = $request->input('image_url');

        $result = $this->vehicleService->deleteImageFromS3ByUrl($imageUrl);

        if (!$result['success']) {
            return response()->json(['error' => $result['message']], $result['status']);
        }

        return response()->json(['message' => $result['message']], $result['status']);
    }

    public function lead(Request $request, LeadService $leadService)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string',
            'vehicle_id' => 'nullable|integer|exists:scs_cars,id',
        ]);

        try {
            $lead = $leadService->createLead($validated);

            return response()->json([
                'message' => 'Lead submitted successfully.',
                'lead' => $lead
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to submit lead.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function archiveVehicles(Request $request)
    {
        $validated = $request->validate([
            'vehicle_ids' => 'required|array',
            'vehicle_ids.*' => 'integer|exists:scs_cars,id',
            'action' => 'required|string|in:archive,restore',
        ]);

        $result = $this->vehicleService->handleArchiveAction($validated['vehicle_ids'], $validated['action']);

        return response()->json([
            'success' => true,
            'message' => $validated['action'] === 'archive'
                ? 'Vehicles archived successfully.'
                : 'Vehicles restored successfully.',
            'data' => $result
        ]);
    }

    public function getArchivedVehicles()
    {
        $archived = ScsCar::onlyTrashed()
            ->with('images') // Include images if needed
            ->orderBy('deleted_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'vehicles' => $archived
        ]);
    }

    public function deleteVehicles(Request $request)
{
    $validated = $request->validate([
        'vehicle_ids' => 'required|array',
        'vehicle_ids.*' => 'integer|exists:scs_cars,id'
    ]);

    $deletedCount = $this->vehicleService->deleteVehiclesByIds($validated['vehicle_ids']);

    return response()->json([
        'success' => true,
        'message' => "$deletedCount vehicle(s) permanently deleted."
    ]);
}

}
