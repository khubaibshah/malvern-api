<?php

namespace App\Services;

use Aws\S3\S3Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File as LaravelFile;
use Illuminate\Http\UploadedFile as LaravelUploadedFile;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;
class AwsS3Service
{
     public function uploadFile(
        LaravelUploadedFile|SymfonyUploadedFile|LaravelFile|string $file,
        string $path,
        ?string $filename = null
    ): string {
        // 1) If a string path is passed, wrap it as a Laravel File
        if (is_string($file)) {
            $file = new LaravelFile($file);
        }

        // 2) If it's a Symfony UploadedFile (and not already Illuminate), convert it
        if ($file instanceof SymfonyUploadedFile && !($file instanceof LaravelUploadedFile)) {
            $file = LaravelUploadedFile::createFromBase($file, /* test */ true);
        }

        // 3) Decide the final filename
        $name = $filename
            ?: ($file instanceof LaravelUploadedFile
                ? ($file->getClientOriginalName() ?: $file->hashName())
                : basename($file->getPathname()));

        $path = trim($path, '/');

        // 4) Upload to S3 with public visibility
        Storage::disk('s3')->putFileAs($path, $file, $name, ['visibility' => 'public']);

        return Storage::disk('s3')->url("$path/$name");
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
