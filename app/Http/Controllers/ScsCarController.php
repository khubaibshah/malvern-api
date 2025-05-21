<?php

namespace App\Http\Controllers;

use App\Models\ScsCar;
use App\Models\ScsCarImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScsCarController extends Controller
{
    public function store(Request $request)
    {
        // Validate car data
        $validator = Validator::make($request->all(), [
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . date('Y'),
            'car_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120', // Validate images, nullable allows no images
            'vrm' => 'nullable|string|max:255',
            'reg_date' => 'nullable|date',
            'man_year' => 'nullable|string|max:255',
            'reg_letter' => 'nullable|string|max:255',
            'variant' => 'nullable|string|max:255',
            'price' => 'required|numeric',
            'plus_vat' => 'nullable|boolean',
            'vat_qualifying' => 'nullable|boolean',
            'was_price' => 'nullable|numeric',
            'trade_price' => 'nullable|string|max:255',
            'trade_text' => 'nullable|string|max:255',
            'price_above_40k' => 'nullable|boolean',
            'mileage' => 'required|integer',
            'engine_cc' => 'nullable|integer',
            'fuel_type' => 'required|string|max:255',
            'body_style' => 'required|string|max:255',
            'colour' => 'required|string|max:255',
            'doors' => 'required|integer',
            'veh_type' => 'required|string|max:255',
            'veh_status' => 'nullable|string|max:255',
            'stock_id' => 'nullable|string|max:255',
            'ebay_gt_title' => 'nullable|string|max:255',
            'subtitle' => 'required|string|max:255',
            'description' => 'required|string'
        ]);

        //error handling
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Create car record
        $car = ScsCar::create($request->only([
            'make', 'model', 'year',
            'car_images.*',
            'vrm',
            'reg_date',
            'man_year',
            'reg_letter',
            'variant',
            'price',
            'plus_vat',
            'vat_qualifying',
            'was_price',
            'trade_price',
            'trade_text',
            'price_above_40k',
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
            'description',
        ]));

        // Get the uploaded images
        $images = $request->file('car_images');

        if ($images) {
            // Validate and store each image
            foreach ($images as $image) {
                $scsCarImage = new ScsCarImage();
                $scsCarImage->car_image = file_get_contents($image);
                $scsCarImage->scs_car_id = $car->id;
                $scsCarImage->save();
            }
        }

        return response()->json(['message' => 'Car and images successfully created', 'car' => $car], 201);
    }
}
