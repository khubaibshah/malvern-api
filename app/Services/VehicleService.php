<?php

namespace App\Services;

use App\Models\ScsCar;
use App\Models\ScsCarImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
            Log::info('Raw file data from request', [
                'hasFile' => $request->hasFile('car_images'),
                'car_images' => $request->file('car_images'),
                'all_files' => $request->allFiles(),
            ]);

            if ($request->hasFile('car_images')) {
                foreach ($request->file('car_images') as $index => $file) {
                    Log::info("Incoming file details", [
                        'index' => $index,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'extension' => $file->getClientOriginalExtension(),
                        'size_kb' => $file->getSize() / 1024,
                        'is_valid' => $file->isValid(),
                        'error_code' => $file->getError(), // very important
                    ]);
                }
            }

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
                'car_images.*' => 'string', // Now expecting S3 keys instead of files            
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

            if ($request->hasFile('car_images')) {
                foreach ($request->file('car_images') as $image) {
                    if ($image->isValid()) {
                        try {
                            $url = $this->awsS3->uploadFile($image, "car_images/{$car->registration}");

                            ScsCarImage::create([
                                'scs_car_id' => $car->id,
                                'car_image' => $url,
                            ]);
                        } catch (\Exception $uploadException) {
                            Log::error('Image upload failed', [
                                'filename' => $image->getClientOriginalName(),
                                'message' => $uploadException->getMessage(),
                            ]);
                            throw $uploadException;
                        }
                    } else {
                        Log::error('Invalid image file encountered', [
                            'filename' => $image->getClientOriginalName()
                        ]);
                    }
                }
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



    //updateVehicleWithImages
    public function updateVehicleWithImages() {}
}
