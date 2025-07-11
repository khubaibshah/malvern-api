<?php

namespace App\Services;

use Aws\S3\S3Client;
use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AwsS3Service
{
    public function uploadFile(UploadedFile $file, string $directory = '', string $disk = 's3'): string
    {
        $filename = Str::random(30) . '.' . $file->getClientOriginalExtension(); // just the file name
        $path = $file->storeAs($directory, $filename, [
            'disk' => $disk,
            'visibility' => 'public',
        ]);
        return Storage::disk($disk)->url($path);
    }

    public function getFileUrl(string $path, string $disk = 's3'): string
    {
        $cdnBaseUrl = config('filesystems.cdn_url', env('AWS_CLOUDFRONT_URL'));

        return rtrim($cdnBaseUrl, '/') . '/' . ltrim($path, '/');
    }


    public function deleteFile(string $path, string $disk = 's3'): bool
    {
        return Storage::disk($disk)->delete($path);
    }

    public function fileExists(string $path, string $disk = 's3'): bool
    {
        return Storage::disk($disk)->exists($path);
    }

    public function listFiles(string $directory, string $disk = 's3'): array
    {
        return Storage::disk($disk)->files($directory);
    }
    public function deleteFolder(string $folder): bool
    {
        $files = Storage::disk('s3')->files($folder);

        if (empty($files)) {
            return true; // Nothing to delete
        }

        return Storage::disk('s3')->delete($files);
    }
}
