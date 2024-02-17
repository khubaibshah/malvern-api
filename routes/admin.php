<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\BookingController;



Route::post('/customerbookings', [BookingController::class, 'store']);