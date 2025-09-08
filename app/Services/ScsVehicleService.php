<?php

namespace App\Services;

use App\Models\ScsCar;
use App\Models\ScsCarImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ScsVehicleService
{
    public function __construct(protected AwsS3Service $awsS3) {}

    public function createVehicleWithImages(Request $request): array
    {
        try {
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
                'car_images.*' => 'string',
                'main_image_index' => 'nullable|integer|min:0',
                'registration_date' => 'nullable|date',
                'gearbox' => 'nullable|string|max:255',
                'keys' => 'nullable|string|max:255',
                'engine_size' => 'nullable|integer|min:0', // <- Added
                'deleted_at' => 'nullable|date'
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed in createVehicleWithImages', [
                    'errors' => $validator->errors()->toArray()
                ]);
                return ['errors' => $validator->errors(), 'status' => 400];
            }

            Log::alert('car data incoming', $request->all());

            $car = ScsCar::create($request->only([
                'registration',
                'make',
                'model',
                'year',
                'vrm',
                'reg_date',
                'registration_date',
                'variant',
                'price',
                'mileage',
                'fuel_type',
                'body_style',
                'colour',
                'doors',
                'gearbox',
                'keys',
                'veh_type',
                'engine_size',
                'description',
                'deleted_at'
            ]));

            $mainImageIndex = $request->input('main_image_index', 0);
            $carImages = $request->input('car_images', []);

            foreach ($carImages as $index => $s3Key) {
                $imageData = [
                    'scs_car_id' => $car->id,
                    'car_image' => $s3Key,
                    'is_main' => $index === $mainImageIndex,
                ];
                ScsCarImage::create($imageData);
            }

            return ['car' => $car->toArray(), 'status' => 201];
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
        try {
            $vehicle = ScsCar::withTrashed()
                ->with(['images' => fn($q) => $q->ordered()]) // is_main DESC, sort_order ASC, id ASC
                ->find($vehicleId);

            if (!$vehicle) {
                return ['error' => 'Vehicle not found', 'status' => 404];
            }

            $folder  = "car_images/{$vehicle->registration}";
            $s3Keys  = $this->awsS3->listFiles($folder) ?? []; // keys like "car_images/REG/xxx.jpg"

            $urls = [];

            if ($vehicle->images && $vehicle->images->count()) {
                // DB-first: keep DB order, prefer S3 per filename if present
                $index = array_flip($s3Keys);

                foreach ($vehicle->images as $img) {
                    $decoded = base64_decode($img->car_image) ?: '';

                    // try to match an S3 key for the same basename in this folder
                    $path     = ltrim(parse_url($decoded, PHP_URL_PATH) ?? '', '/');
                    $basename = $path ? basename($path) : null;
                    $guessKey = $basename ? "{$folder}/{$basename}" : null;

                    if ($guessKey && isset($index[$guessKey])) {
                        $urls[] = $this->awsS3->getFileUrl($guessKey);
                    } elseif ($path && isset($index[$path])) {
                        $urls[] = $this->awsS3->getFileUrl($path);
                    } elseif ($decoded !== '') {
                        $urls[] = $decoded; // fall back to the decoded DB URL
                    }
                }
            }

            // If DB branch produced nothing, or we had no DB rows, fall back to S3 listing
            if (empty($urls) && !empty($s3Keys)) {
                usort($s3Keys, fn($a, $b) => strnatcmp(basename($a), basename($b)));
                $urls = array_map(fn($k) => $this->awsS3->getFileUrl($k), $s3Keys);
            }

            // de-dup while preserving order
            $urls = array_values(array_unique($urls));

            // Main image: prefer DB main, else first URL
            $main = null;
            if ($vehicle->relationLoaded('images') && $vehicle->images->count()) {
                $mainRow = $vehicle->images->firstWhere('is_main', true) ?? $vehicle->images->first();
                if ($mainRow) {
                    $decoded  = base64_decode($mainRow->car_image) ?: null;
                    if ($decoded) {
                        $mainPath = ltrim(parse_url($decoded, PHP_URL_PATH) ?? '', '/');
                        $guess    = $mainPath ? "{$folder}/" . basename($mainPath) : null;

                        if ($mainPath && in_array($mainPath, $s3Keys, true)) {
                            $main = $this->awsS3->getFileUrl($mainPath);
                        } elseif ($guess && in_array($guess, $s3Keys, true)) {
                            $main = $this->awsS3->getFileUrl($guess);
                        } else {
                            $main = $decoded;
                        }
                    }
                }
            }
            if (!$main && !empty($urls)) {
                $main = $urls[0];
            }

            // 👉 Build a plain array so the relation doesn’t overwrite our images
            $car = $vehicle->toArray();
            $car['images'] = $urls;        // array of strings (ordered)
            $car['main_image'] = $main;    // string

            return [
                'message' => 'Vehicle loaded successfully',
                'car'     => $car,
                'status'  => 200,
            ];
        } catch (\Throwable $e) {
            Log::error('Failed to fetch vehicle with images', ['error' => $e->getMessage()]);
            return ['error' => 'Server error', 'status' => 500];
        }
    }


    public function getAll(): array
    {
        try {
            $vehicles = ScsCar::withTrashed()->get();

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

    public function getAllVehicles(): array
    {
        try {
            // 1) Get cars + their DB images (main first, then sort_order)
            $cars = \App\Models\ScsCar::with([
                'images' => fn($q) => $q->ordered()->select('id', 'scs_car_id', 'car_image', 'is_main', 'sort_order')
            ])->latest()->get();

            $cars->transform(function ($car) {
                // If DB has images, use them (already ordered by scope)
                if ($car->images && $car->images->count() > 0) {
                    // expose a single main_image for convenience
                    $car->setAttribute('main_image', optional($car->images->first())->car_image);
                    return $car;
                }

                // 2) Fallback to S3 if no DB images
                $folder = "car_images/{$car->registration}";
                $keys   = $this->awsS3->listFiles($folder) ?? [];

                // Keep only image-like keys
                $keys = array_values(array_filter($keys, function ($k) {
                    $ext = strtolower(pathinfo($k, PATHINFO_EXTENSION));
                    return in_array($ext, ['jpg', 'jpeg', 'png', 'webp']);
                }));

                // Natural sort for human-friendly order (prefixes like 0_, 1_ … will naturally align)
                usort($keys, fn($a, $b) => strnatcasecmp(basename($a), basename($b)));

                // Build a consistent "images" collection (matching your ScsCarImage shape)
                $fallback = collect($keys)->map(function ($key, $i) use ($car) {
                    $url = $this->awsS3->getFileUrl($key);

                    return [
                        'id'         => null,                 // no DB id for S3-only entries
                        'scs_car_id' => $car->id,
                        // match accessor behavior (car_image is base64 encoded in your model)
                        'car_image'  => base64_encode($url),
                        'is_main'    => $i === 0,
                        'sort_order' => $i,
                    ];
                });

                // Attach as relation so frontend can read car.images
                $car->setRelation('images', $fallback);
                // Expose main imagem 
                $car->setAttribute('main_image', optional($fallback->first())['car_image'] ?? null);

                return $car;
            });

            return ['cars' => $cars, 'status' => 200];
        } catch (\Throwable $e) {
            Log::error('Failed to fetch all vehicles', ['error' => $e->getMessage()]);
            return [
                'error'  => 'Server error while fetching vehicles.',
                'status' => 500,
            ];
        }
    }




    public function deleteImageFromS3ByUrl(string $imageUrl): array
    {
        $baseUrl = Storage::disk('s3')->url('');
        $s3Key = str_replace($baseUrl, '', $imageUrl);

        if (!app(AwsS3Service::class)->fileExists($s3Key)) {
            return [
                'success' => false,
                'message' => 'File does not exist on S3.',
                'status' => 404
            ];
        }

        $deleted = $this->awsS3->deleteFile($s3Key);

        return $deleted
            ? ['success' => true, 'message' => 'Image deleted successfully.', 'status' => 200]
            : ['success' => false, 'message' => 'Failed to delete image from S3.', 'status' => 500];
    }
    //updateVehicleWithImages
    public function updateVehicleWithImages(Request $request, int $vehicleId): array
    {
        try {
            $validator = Validator::make($request->all(), [
                'make' => 'required|string|max:255',
                'model' => 'required|string|max:255',
                'year' => 'required|integer|min:1900|max:' . date('Y'),
                'registration' => 'required|string|max:255',
                'deleted_at' => 'nullable|date'
            ]);

            if ($validator->fails()) {
                return ['errors' => $validator->errors(), 'status' => 422];
            }

            $car = ScsCar::withTrashed()->find($vehicleId);

            if (!$car) {
                return ['error' => 'Vehicle not found', 'status' => 404];
            }

            //Update vehicle details including engine_size
            $car->update($request->only([
                'make',
                'model',
                'year',
                'registration',
                'variant',
                'price',
                'mileage',
                'fuel_type',
                'colour',
                'doors',
                'veh_type',
                'description',
                'body_style',
                'gearbox',
                'keys',
                'registration_date',
                'engine_size',
                'deleted_at'
            ]));

            // Replace images if provided
            if ($request->has('car_images')) {
                ScsCarImage::withTrashed()->where('scs_car_id', $car->id)->delete();

                $images = $request->input('car_images', []);
                $mainImageKey = $request->input('main_image');

                foreach ($images as $imageKey) {
                    ScsCarImage::create([
                        'scs_car_id' => $car->id,
                        'car_image' => $imageKey,
                        'is_main' => $imageKey === $mainImageKey,
                    ]);
                }

                if ($mainImageKey) {
                    $car->main_image = $mainImageKey;
                    $car->save();
                }
            }

            return ['message' => 'Vehicle updated successfully', 'car' => $car->fresh(), 'status' => 200];
        } catch (\Exception $e) {
            Log::error('Exception in updateVehicleWithImages', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'error' => 'Server error occurred while updating vehicle.',
                'message' => $e->getMessage(),
                'status' => 500
            ];
        }
    }

    public function handleArchiveAction(array $ids, string $action)
    {
        if ($action === 'archive') {
            // Soft delete cars
            $cars = ScsCar::whereIn('id', $ids)->get();

            foreach ($cars as $car) {
                $car->images()->delete(); // Soft delete related images
                $car->delete(); // Soft delete the car itself
            }

            return true;
        }

        if ($action === 'restore') {
            // Restore cars
            $cars = ScsCar::onlyTrashed()->whereIn('id', $ids)->get();

            foreach ($cars as $car) {
                $car->restore(); // Restore car
                $car->images()->withTrashed()->restore(); // Restore related images
            }

            return true;
        }

        return false;
    }

    public function deleteVehiclesByIds(array $ids): int
    {
        // Fetch the vehicles (even if soft deleted)
        $cars = ScsCar::withTrashed()->whereIn('id', $ids)->get();

        foreach ($cars as $car) {
            // Delete related images from S3
            if (!empty($car->registration)) {
                $folder = "car_images/{$car->registration}";
                $this->awsS3->deleteFolder($folder); // <- Deletes all images in the folder
            }

            // Delete related database image records
            $car->images()->delete(); // or forceDelete() if applicable
        }

        // Permanently delete the vehicles
        return ScsCar::withTrashed()->whereIn('id', $ids)->forceDelete();
    }
}
