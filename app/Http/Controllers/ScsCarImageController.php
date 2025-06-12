<?php

namespace App\Http\Controllers;

use App\Models\ScsCar;
use App\Models\ScsCarImage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ScsCarImageController extends Controller
{
    /**
     * Store uploaded images for a car in S3 and save URLs to DB.
     */
    public function store(Request $request, $carId)
    {
        $validator = Validator::make($request->all(), [
            'car_images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $car = ScsCar::find($carId);
        if (!$car) {
            return response()->json(['message' => 'Car not found'], 404);
        }

        $uploadedPaths = [];

        foreach ($request->file('car_images') as $image) {
            $path = $image->store("car_images/{$carId}", 's3'); // uploads to S3
            $url = Storage::disk('s3')->url($path);             // get public or signed URL
            $uploadedPaths[] = $url;
        }

        return response()->json([
            'message' => 'Images uploaded to S3 successfully',
            'files' => $uploadedPaths
        ], 201);
    }


    /**
     * Get a single image by ID.
     */
    public function show($id)
    {
        $scsCarImage = ScsCarImage::find($id);

        if (!$scsCarImage) {
            return response()->json(['message' => 'Image not found'], 404);
        }

        return response()->json(['image' => $scsCarImage->car_image]);
    }

    /**
     * Get all car images.
     */
    public function getAllCarsImages(): JsonResponse
    {
        return response()->json(ScsCarImage::all());
    }

    /**
     * Get full car details with images by ID.
     */
    public function getCarById($vehicleId): JsonResponse
    {
        $vehicle = ScsCar::with('images')->find($vehicleId);

        if (!$vehicle) {
            return response()->json(['message' => 'Car not found'], 404);
        }

        return response()->json($vehicle);
    }

    /**
     * Update vehicle listing (TODO).
     */
    public function updateVehicleListing(): JsonResponse
    {
        // You can implement this as needed.
        return response()->json(['message' => 'Update not implemented'], 501);
    }

    /**
     * Get all cars with images.
     */
    public function getAllCars(): JsonResponse
    {
        return response()->json(ScsCar::get());
    }
}
