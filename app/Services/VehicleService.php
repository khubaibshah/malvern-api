<?php

namespace App\Services;

use App\Models\ScsCar;
use App\Models\ScsCarImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VehicleService
{
    public function __construct(protected AwsS3Service $awsS3) {}

    public function createVehicleWithImages(Request $request): array
    {
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
            'car_images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',        
        ]);

        if ($validator->fails()) {
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

        if ($request->has('car_images')) {
            foreach ($request->file('car_images') as $image) {
                if ($image->isValid()) {
                    $url = $this->awsS3->uploadFile($image, "car_images/{$car->registration}");
                    ScsCarImage::create([
                        'scs_car_id' => $car->id,
                        'car_image' => $url,
                    ]);
                }
            }
        }

        return ['car' => $car, 'status' => 201];
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
    public function updateVehicleWithImages()
    {

    }
}
