<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::all();
        return response()->json($bookings);
        var_dump('testing', $bookings);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:255',
            'vehicle_make_model' => 'required|string|max:255',
            'booking_datetime' => 'required|date',
        ]);

        $booking = Booking::create($request->all());

        return response()->json($booking, 201);
    }
}
