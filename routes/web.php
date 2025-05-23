<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Aws\S3\S3Client;


Route::get('/', function () {
    return view('welcome');
});
// Route::get('/{any}', function () {
//     return view('index');
// })->where('any', '.*');
// Route::middleware('web')->group(function () {
//     // Authentication routes
//     // Route::get('/login', 'Auth\LoginController@showLoginForm');
//     Route::post('/login', 'Auth\LoginController@login')->name('login');
//     Route::post('/logout', 'Auth\LoginController@logout')->name('logout');
//     // Other routes...
// });




Route::get('/s3-debug', function() {
    try {
        $s3Client = new \Aws\S3\S3Client([
            'version' => 'latest',
            'region'  => env('AWS_DEFAULT_REGION', 'eu-west-2'),
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
            'http' => [
                'verify' => storage_path('certificates/cacert.pem') // Use Laravel's storage path
            ]
        ]);

        $result = $s3Client->listObjectsV2([
            'Bucket' => env('AWS_BUCKET', 'scs-vehicle-images'),
            'MaxKeys' => 5
        ]);

        return response()->json([
            'status' => 'success',
            'objects' => $result->get('Contents') ?? []
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'certificate_path' => storage_path('certificates/cacert.pem'),
            'file_exists' => file_exists(storage_path('certificates/cacert.pem')),
            'file_size' => file_exists(storage_path('certificates/cacert.pem')) 
                          ? filesize(storage_path('certificates/cacert.pem')) 
                          : 0
        ], 500);
    }
});


Route::get('/cert-check', function() {
    $path = storage_path('certificates/cacert.pem');
    return [
        'path' => $path,
        'exists' => file_exists($path),
        'readable' => is_readable($path),
        'size' => filesize($path),
        'modified' => date('Y-m-d H:i:s', filemtime($path))
    ];
});