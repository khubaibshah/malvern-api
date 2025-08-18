<?php

namespace App\Jobs;

use App\Models\ScsCar;
use App\Models\ScsCarImage;
use App\Services\AwsS3Service;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProcessVehicleImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $carId;
    protected array $image;
    protected int $index;

    public function __construct(int $carId, array $image, int $index)
    {
        $this->carId = $carId;
        $this->image = $image;
        $this->index = $index;
    }

    public function handle(AwsS3Service $awsS3): void
    {
        try {
            Log::info("Running image job for car_id {$this->carId}, index {$this->index}");

            $car = ScsCar::find($this->carId);
            if (!$car) return;

            $imgUrl = str_replace('{resize}', 'w800h600', $this->image['href']);

            $imageContents = file_get_contents($imgUrl);
            if (!$imageContents) return;

            $tempPath = sys_get_temp_dir() . '/' . Str::random(40) . '.jpg';
            file_put_contents($tempPath, $imageContents);

            $uploadedFile = new UploadedFile(
                $tempPath,
                basename($tempPath),
                'image/jpeg',
                null,
                true
            );

            $s3Url = $awsS3->uploadFile($uploadedFile, "car_images/{$car->registration}");

            ScsCarImage::updateOrCreate(
                [
                    'scs_car_id' => $car->id,
                    'sort_order' => $this->index,
                ],
                [
                    'car_image' => $s3Url,
                    'is_main' => $this->index === 0,
                ]
            );

            // Clean up temp file
            unlink($tempPath);
        } catch (\Throwable $e) {
            Log::error('Failed to process vehicle image', [
                'car_id' => $this->carId,
                'index' => $this->index,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
