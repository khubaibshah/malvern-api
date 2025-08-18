<?php

namespace App\Http\Controllers;

use App\Services\ScsCarImageService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ScsCarImageController extends Controller
{
    protected ScsCarImageService $imageService;

    public function __construct(ScsCarImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function store(Request $request, int $carId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'car_images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $result = $this->imageService->uploadCarImages($carId, $request->file('car_images'));

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], 404);
        }

        return response()->json([
            'message' => 'Images uploaded successfully',
            'files' => $result['files'],
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $image = $this->imageService->getImageById($id);

        if (!$image) {
            return response()->json(['message' => 'Image not found'], 404);
        }

        return response()->json(['image' => $image->car_image]);
    }

    public function getAllCarsImages(): JsonResponse
    {
        return response()->json($this->imageService->getAllImages());
    }

    public function getCarById(int $vehicleId): JsonResponse
    {
        $car = $this->imageService->getCarWithImages($vehicleId);

        if (!$car) {
            return response()->json(['message' => 'Car not found'], 404);
        }

        return response()->json($car);
    }

    public function getAllCars(): JsonResponse
    {
        return response()->json($this->imageService->getAllCarsWithImages());
    }

    public function updateVehicleListing(): JsonResponse
    {
        return response()->json(['message' => 'Update not implemented'], 501);
    }
}
