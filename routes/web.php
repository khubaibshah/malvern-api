<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
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
