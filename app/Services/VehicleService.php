<?php

namespace App\Services;

use App\Models\ScsCar;
use App\Models\ScsCarImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class VehicleService
{
    public function __construct(protected AwsS3Service $awsS3)
    {
        Log::info('VehicleService constructor called');
    }

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
                'car_images.*' => 'string', // Expecting S3 keys
                'main_image_index' => 'nullable|integer|min:0', // New optional field
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed in createVehicleWithImages', [
                    'errors' => $validator->errors()->toArray()
                ]);
                return ['errors' => $validator->errors(), 'status' => 400];
            }

            $car = ScsCar::create($request->only([
                'registration',
                'make',
                'model',
                'year',
                'vrm',
                'reg_date',
                'man_year',
                'variant',
                'price',
                'was_price',
                'mileage',
                'engine_cc',
                'fuel_type',
                'body_style',
                'colour',
                'doors',
                'veh_type',
                'veh_status',
                'stock_id',
                'ebay_gt_title',
                'subtitle',
                'description'
            ]));

            $mainImageIndex = $request->input('main_image_index', 0);
            dd($mainImageIndex);
            $carImages = $request->input('car_images', []);

            foreach ($carImages as $index => $s3Key) {
                $imageData = [
                    'scs_car_id' => $car->id,
                    'car_image' => $s3Key,
                    'is_main' => $index === $mainImageIndex, // Set main image flag
                ];
                // dd($imageData);
                ScsCarImage::create($imageData);
            }

            return ['car' => $car, 'status' => 201];
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
        $vehicle = ScsCar::find($vehicleId);

        if (!$vehicle) {
            return ['error' => 'Vehicle not found', 'status' => 404];
        }
        $folder = "car_images/{$vehicle->registration}";
        $imagePaths = $this->awsS3->listFiles($folder);

        // Generate public URLs for each image
        $imageUrls = array_map(fn($path) => $this->awsS3->getFileUrl($path), $imagePaths);

        // Add image URLs to the response
        $vehicle->images = $imageUrls;

        return ['message' => 'Vehicle updated successfully', 'car' => $vehicle, 'status' => 200];
    }

    public function getAll(): array
    {
        try {
            $vehicles = ScsCar::all();

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
                'variant' => 'nullable|string|max:255',
                'price' => 'required|numeric',
                'mileage' => 'required|integer',
                'fuel_type' => 'required|string|max:255',
                'colour' => 'required|string|max:255',
                'doors' => 'nullable|integer|min:0|max:10',
                'veh_type' => 'required|string|max:255',
                'description' => 'required|string',
                'car_images' => 'nullable|array',
                'car_images.*' => 'string', // S3 keys
                'main_image' => 'nullable|string', // main image key
            ]);

            if ($validator->fails()) {
                return ['errors' => $validator->errors(), 'status' => 422];
            }

            $car = ScsCar::find($vehicleId);

            if (!$car) {
                return ['error' => 'Vehicle not found', 'status' => 404];
            }

            // Update vehicle details
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
                'description'
            ]));

            // Remove old images and replace with new ones (if images are provided)
            if ($request->has('car_images')) {
                ScsCarImage::where('scs_car_id', $car->id)->delete();

                foreach ($request->input('car_images', []) as $imageKey) {
                    ScsCarImage::create([
                        'scs_car_id' => $car->id,
                        'car_image' => $imageKey,
                        'is_main' => $imageKey === $request->input('main_image'),
                    ]);
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
}
