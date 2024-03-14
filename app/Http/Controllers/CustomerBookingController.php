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
            'Booking_reference' => 'required|numeric|max:255',
            'vehicle_make' => 'required|string|max:255',
            'vehicle_model' => 'required|string|max:255',
            'job_repair_id' => 'required|numeric|max:255',
            'bookings_datetime' => 'required|date|max:255',
            'customer_notes' => 'nullable|string|max:255',
            'deposit_paid' => 'required|numeric|max:255',
            'repair_price' => 'required|numeric|max:255',
        ]);

        // Create a new customer booking
        $customerBooking = CustomerBooking::create($request->all());

        return response()->json($customerBooking, 201);
    }
}
