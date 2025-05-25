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
            Log::info('createVehicleWithImages: starting validation');
            Log::info('Raw car_images', ['input' => $request->input('car_images')]);
            Log::info('Files car_images', ['files' => $request->file('car_images')]);

            // Inspect file info before validating
            if ($request->hasFile('car_images')) {
                foreach ($request->file('car_images') as $file) {
                    Log::info('Pre-validation MIME type', ['mime' => $file->getMimeType()]);
                    Log::info('Pre-validation extension', ['ext' => $file->getClientOriginalExtension()]);
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
                'car_images.*' => 'file|mimes:jpeg,png,jpg,gif,svg,avif|max:5120',

            ]);

            Log::info('createVehicleWithImages: finished validation');

            if ($validator->fails()) {
                Log::warning('Validation failed', ['errors' => $validator->errors()->toArray()]);
                return ['errors' => $validator->errors(), 'status' => 400];
            }

            Log::info('createVehicleWithImages: creating car');
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

            Log::info('createVehicleWithImages: uploading images');
            if ($request->hasFile('car_images')) {
                foreach ($request->file('car_images') as $image) {
                    Log::info('File MIME type', ['mime' => $image->getMimeType()]);
                    Log::info('File extension', ['ext' => $image->getClientOriginalExtension()]);
                    Log::info('createVehicleWithImages: processing image');

                    if ($image->isValid()) {
                        Log::info('createVehicleWithImages: image is valid, uploading to S3');
                        $url = $this->awsS3->uploadFile($image, "car_images/{$car->registration}");
                        Log::info('createVehicleWithImages: S3 upload done', ['url' => $url]);

                        ScsCarImage::create([
                            'scs_car_id' => $car->id,
                            'car_image' => $url,
                        ]);
                    } else {
                        Log::warning('createVehicleWithImages: image is not valid', [
                            'originalName' => $image->getClientOriginalName(),
                            'mime' => $image->getMimeType()
                        ]);
                    }
                }
            }

            Log::info('createVehicleWithImages: complete');
            return ['car' => $car, 'status' => 201];
        } catch (\Exception $e) {
            Log::error('createVehicleWithImages failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
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

    //updateVehicleWithImages
    public function updateVehicleWithImages() {}
}
