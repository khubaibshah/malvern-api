<?php

namespace App\Http\Controllers;

use App\Models\AdminBooking;
use Illuminate\Http\Request;

class AdminBookingController extends Controller
{
    public function index()
    {
        $bookings = AdminBooking::all();
        return response()->json($bookings);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:255',
            'vehicle_make_model' => 'required|string|max:255',
            'booking_datetime' => 'required|date',
            'notes' => 'required|string|max:255',
        ]);

        $booking = AdminBooking::create($request->all());

        return response()->json($booking, 201);
    }
}
