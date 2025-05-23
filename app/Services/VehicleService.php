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
            'car_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors(), 'status' => 400];
        }

        $car = ScsCar::create($request->only([
            'registration', 'make', 'model', 'year', 'vrm', 'reg_date', 'man_year',
            'variant', 'price', 'was_price', 'mileage', 'engine_cc', 'fuel_type',
            'body_style', 'colour', 'doors', 'veh_type', 'veh_status',
            'stock_id', 'ebay_gt_title', 'subtitle', 'description'
        ]));

        if ($request->hasFile('car_images')) {
            foreach ($request->file('car_images') as $image) {
                $url = $this->awsS3->uploadFile($image, "car_images/{$car->registration}");

                ScsCarImage::create([
                    'scs_car_id' => $car->id,
                    'car_image' => $url,
                ]);
            }
        }

        return ['car' => $car, 'status' => 201];
    }
}
