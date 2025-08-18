<?php

namespace App\Services;

use App\Models\ScsCar;
use App\Models\ScsCarImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ScsCarImageService
{
    /**
     * Upload and store multiple car images.
     */
    public function uploadCarImages(int $carId, array $images): array
    {
        $car = ScsCar::find($carId);
        if (!$car) {
            return ['error' => 'Car not found'];
        }

        $uploadedPaths = [];

        foreach ($images as $image) {
            if ($image instanceof UploadedFile) {
                $path = $image->store("car_images/{$carId}", 's3');
                $url = Storage::disk('s3')->url($path);

                ScsCarImage::create([
                    'car_id' => $carId,
                    'car_image' => $url,
                ]);

                $uploadedPaths[] = $url;
            }
        }

        return ['files' => $uploadedPaths];
    }

    public function getImageById(int $id): ?ScsCarImage
    {
        return ScsCarImage::find($id);
    }

    public function getAllImages()
    {
        return ScsCarImage::all();
    }

    public function getCarWithImages(int $vehicleId): ?ScsCar
    {
        return ScsCar::with('images')->find($vehicleId);
    }

    public function getAllCarsWithImages()
    {
        return ScsCar::with('images')->get();
    }
}
