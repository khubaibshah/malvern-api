<?php

namespace App\Http\Controllers;
use App\Models\CustomerBooking;
use Illuminate\Http\Request;

class CustomerBookingController extends Controller
{
    public function index()
    {
        // Retrieve all customer bookings
        $customerBookings = CustomerBooking::all();

        // Return view with bookings data
        return response()->json($customerBookings);
    }

    public function store(Request $request)
    {
        $request->validate([
            'Booking_refrence' => 'required|string',
            'vehicle_make' => 'required|string',
            'vehicle_model' => 'required|string',
            'job_repair_id' => 'required|numeric',
            'bookings_datetime' => 'required|date',
            'customer_notes' => 'nullable|string',
            'deposit_paid' => 'required|numeric',
            'repair_price' => 'required|numeric',
        ]);

        // Create a new customer booking
        $customerBooking = CustomerBooking::create($request->all());

        return response()->json(['message' => 'Booking created successfully', 'data' => $customerBooking], 201);
    }
}
