<?php

namespace App\Services;

use App\Models\ScsCar;
use App\Models\ScsCarImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ScsVehicleService
{
    public function __construct(protected AwsS3Service $awsS3){}

    public function createVehicleWithImages(Request $request): array
    {
        try {
            $validator = Validator::make($request->all(), [
                'make' => 'required|string|max:255',
                'model' => 'required|string|max:255',
                'year' => 'required|integer|min:1900|max:' . date('Y'),
                'vrm' => 'nullable|string|max:255',
                'reg_date' => 'nullable|date',
                'registration' => 'nullable|string|max:255',
                'variant' => 'nullable|string|max:255',
                'price' => 'required|numeric',
                'mileage' => 'required|integer',
                'fuel_type' => 'required|string|max:255',
                'colour' => 'required|string|max:255',
                'veh_type' => 'required|string|max:255',
                'veh_status' => 'nullable|string|max:255',
                'description' => 'required|string',
                'car_images' => 'required|array',
                'car_images.*' => 'string',
                'main_image_index' => 'nullable|integer|min:0',
                'registration_date' => 'nullable|date',
                'gearbox' => 'nullable|string|max:255',
                'keys' => 'nullable|string|max:255',
                'engine_size' => 'nullable|integer|min:0', // <- Added
                'deleted_at' => 'nullable|date'
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed in createVehicleWithImages', [
                    'errors' => $validator->errors()->toArray()
                ]);
                return ['errors' => $validator->errors(), 'status' => 400];
            }

            Log::alert('car data incoming', $request->all());

            $car = ScsCar::create($request->only([
                'registration',
                'make',
                'model',
                'year',
                'vrm',
                'reg_date',
                'registration_date',
                'variant',
                'price',
                'mileage',
                'fuel_type',
                'body_style',
                'colour',
                'doors',
                'gearbox',
                'keys',
                'veh_type',
                'engine_size',
                'description',
                'deleted_at'
            ]));

            $mainImageIndex = $request->input('main_image_index', 0);
            $carImages = $request->input('car_images', []);

            foreach ($carImages as $index => $s3Key) {
                $imageData = [
                    'scs_car_id' => $car->id,
                    'car_image' => $s3Key,
                    'is_main' => $index === $mainImageIndex,
                ];
                ScsCarImage::create($imageData);
            }

            return ['car' => $car->toArray(), 'status' => 201];
        } catch (\Exception $e) {
            Log::error('Exception in createVehicleWithImages', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'error' => 'Server error occurred while creating vehicle.',
                'message' => $e->getMessage(),
                'status' => 500
            ];
        }
    }

public function getVehicleWithImages(int $vehicleId): array
{
    $vehicle = ScsCar::withTrashed()->find($vehicleId);

    if (!$vehicle) {
        return ['error' => 'Vehicle not found', 'status' => 404];
    }

    $folder = "car_images/{$vehicle->registration}";

    // Step 1: Check if S3 images exist
    $s3Paths = $this->awsS3->listFiles($folder);

    if (!empty($s3Paths)) {
        // ✅ Return only S3 images
        $imageUrls = array_map(fn($path) => $this->awsS3->getFileUrl($path), $s3Paths);
    } else {
        // ✅ No S3 images — fallback to DB (AutoTrader)
        $dbUrls = ScsCarImage::where('scs_car_id', $vehicle->id)
            ->orderBy('sort_order')
            ->get()
            ->map(fn($img) => base64_decode($img->car_image))
            ->toArray();

        $imageUrls = $dbUrls;
    }

    $vehicle->images = array_values(array_unique($imageUrls));

    return [
        'message' => 'Vehicle loaded successfully',
        'car' => $vehicle,
        'status' => 200
    ];
}



    public function getAll(): array
    {
        try {
            $vehicles = ScsCar::withTrashed()->get();

            $carsWithImages = $vehicles->map(function ($car) {
                $folder = "car_images/{$car->registration}";
                $imagePaths = $this->awsS3->listFiles($folder);
                $imageUrls = array_map(fn($path) => $this->awsS3->getFileUrl($path), $imagePaths);

                $car->images = $imageUrls;
                return $car;
            });

            return ['cars' => $carsWithImages, 'status' => 200];
        } catch (\Exception $e) {
            Log::error('Failed to fetch all vehicles', ['error' => $e->getMessage()]);
            return [
                'error' => 'Server error while fetching vehicles.',
                'status' => 500
            ];
        }
    }

    public function getAllVehicles(): array
    {
        try {
            $vehicles = ScsCar::get();

            $carsWithImages = $vehicles->map(function ($car) {
                $folder = "car_images/{$car->registration}";
                $imagePaths = $this->awsS3->listFiles($folder);
                $imageUrls = array_map(fn($path) => $this->awsS3->getFileUrl($path), $imagePaths);

                $car->images = $imageUrls;
                return $car;
            });

            return ['cars' => $carsWithImages, 'status' => 200];
        } catch (\Exception $e) {
            Log::error('Failed to fetch all vehicles', ['error' => $e->getMessage()]);
            return [
                'error' => 'Server error while fetching vehicles.',
                'status' => 500
            ];
        }
    }


    public function deleteImageFromS3ByUrl(string $imageUrl): array
    {
        $baseUrl = Storage::disk('s3')->url('');
        $s3Key = str_replace($baseUrl, '', $imageUrl);

        if (!app(AwsS3Service::class)->fileExists($s3Key)) {
            return [
                'success' => false,
                'message' => 'File does not exist on S3.',
                'status' => 404
            ];
        }

        $deleted = $this->awsS3->deleteFile($s3Key);

        return $deleted
            ? ['success' => true, 'message' => 'Image deleted successfully.', 'status' => 200]
            : ['success' => false, 'message' => 'Failed to delete image from S3.', 'status' => 500];
    }
    //updateVehicleWithImages
    public function updateVehicleWithImages(Request $request, int $vehicleId): array
    {
        try {
            $validator = Validator::make($request->all(), [
                'make' => 'required|string|max:255',
                'model' => 'required|string|max:255',
                'year' => 'required|integer|min:1900|max:' . date('Y'),
                'registration' => 'required|string|max:255',
                'deleted_at' => 'nullable|date'
            ]);

            if ($validator->fails()) {
                return ['errors' => $validator->errors(), 'status' => 422];
            }

            $car = ScsCar::withTrashed()->find($vehicleId);

            if (!$car) {
                return ['error' => 'Vehicle not found', 'status' => 404];
            }

            //Update vehicle details including engine_size
            $car->update($request->only([
                'make',
                'model',
                'year',
                'registration',
                'variant',
                'price',
                'mileage',
                'fuel_type',
                'colour',
                'doors',
                'veh_type',
                'description',
                'body_style',
                'gearbox',
                'keys',
                'registration_date',
                'engine_size',
                'deleted_at'
            ]));

            // Replace images if provided
            if ($request->has('car_images')) {
                ScsCarImage::withTrashed()->where('scs_car_id', $car->id)->delete();

                $images = $request->input('car_images', []);
                $mainImageKey = $request->input('main_image');

                foreach ($images as $imageKey) {
                    ScsCarImage::create([
                        'scs_car_id' => $car->id,
                        'car_image' => $imageKey,
                        'is_main' => $imageKey === $mainImageKey,
                    ]);
                }

                if ($mainImageKey) {
                    $car->main_image = $mainImageKey;
                    $car->save();
                }
            }

            return ['message' => 'Vehicle updated successfully', 'car' => $car->fresh(), 'status' => 200];
        } catch (\Exception $e) {
            Log::error('Exception in updateVehicleWithImages', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'error' => 'Server error occurred while updating vehicle.',
                'message' => $e->getMessage(),
                'status' => 500
            ];
        }
    }

    public function handleArchiveAction(array $ids, string $action)
    {
        if ($action === 'archive') {
            // Soft delete cars
            $cars = ScsCar::whereIn('id', $ids)->get();

            foreach ($cars as $car) {
                $car->images()->delete(); // Soft delete related images
                $car->delete(); // Soft delete the car itself
            }

            return true;
        }

        if ($action === 'restore') {
            // Restore cars
            $cars = ScsCar::onlyTrashed()->whereIn('id', $ids)->get();

            foreach ($cars as $car) {
                $car->restore(); // Restore car
                $car->images()->withTrashed()->restore(); // Restore related images
            }

            return true;
        }

        return false;
    }

    public function deleteVehiclesByIds(array $ids): int
    {
        // Fetch the vehicles (even if soft deleted)
        $cars = ScsCar::withTrashed()->whereIn('id', $ids)->get();

        foreach ($cars as $car) {
            // Delete related images from S3
            if (!empty($car->registration)) {
                $folder = "car_images/{$car->registration}";
                $this->awsS3->deleteFolder($folder); // <- Deletes all images in the folder
            }

            // Delete related database image records
            $car->images()->delete(); // or forceDelete() if applicable
        }

        // Permanently delete the vehicles
        return ScsCar::withTrashed()->whereIn('id', $ids)->forceDelete();
    }
}
