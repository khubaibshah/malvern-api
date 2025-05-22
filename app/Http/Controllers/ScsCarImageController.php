<?php

// app/Http/Controllers/ScsCarImageController.php
namespace App\Http\Controllers;

use App\Models\ScsCarImage;
use App\Models\ScsCar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScsCarImageController extends Controller
{
    /**
     * Store a newly created vehicle in storage.
     */
    public function store(Request $request, $carId)
    {
        $validator = Validator::make($request->all(), [
            'car_images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // 2MB max per image
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $car = ScsCar::find($carId);
        if (!$car) {
            return response()->json(['message' => 'Car not found'], 404);
        }

        $images = $request->file('car_images');

        foreach ($images as $image) {
            $scsCarImage = new ScsCarImage();
            $scsCarImage->car_image = file_get_contents($image);
            $scsCarImage->scs_car_id = $carId;
            $scsCarImage->save();
        }

        return response()->json(['message' => 'Images successfully uploaded'], 201);
    }

    /**
     * Display the vehicle record with no images
     */
    public function show($id)
    {
        $scsCarImage = ScsCarImage::find($id);

        if (!$scsCarImage) {
            return response()->json(['message' => 'Image not found'], 404);
        }

        return response()->json(['image' => $scsCarImage->car_image]);
    }

    // Retrieve all images for cars 
    public function getAllCarsImages() : JsonResponse {
        $scsCars = ScsCarImage::all();
        return response()->json($scsCars); 
    }
    
    //get full car details by id
    public function getCarById($vehicleId): JsonResponse
    {
        $vehicle = ScsCar::with('images')->find($vehicleId);
        if (!$vehicle) {
            return response()->json(['message' => 'Car not found'], 404);
        }

        return response()->json($vehicle);
    }

    //edit vehicle listing from admin
    public function updateVehicleListing(): JsonResponse
    {


    }

    //get all cars with images
    public function getAllCars(): JsonResponse
    {
        $allCars = ScsCar::with('images')->get();
        return response()->json($allCars);
    }
}

