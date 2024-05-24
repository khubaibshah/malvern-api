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
        $request->validate([
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . date('Y'),
            'car_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validate images, nullable allows no images
        ]);

        // Create car record
        $car = ScsCar::create($request->only(['make', 'model', 'year']));

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
