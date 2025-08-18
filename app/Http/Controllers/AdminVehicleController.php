<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class AdminVehicleController extends Controller
{
    public function index()
    {
        // Fetch all vehicles from the database
        $vehicles = Vehicle::all();

        // Return a JSON response with the fetched vehicles
        return response()->json($vehicles);
    }

    public function show($registrationNumber)
    {
        // Fetch a single vehicle by its registration number from the database
        $vehicle = Vehicle::where('registration_number', $registrationNumber)->first();

        if (!$vehicle) {
            // If the vehicle with the provided registration number is not found, return a 404 response
            return response()->json(['error' => 'Vehicle not found'], 404);
        }

        // Return a JSON response with the fetched vehicle
        return response()->json($vehicle);
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'registration_number' => 'required|string|max:255',
            'make' => 'required|string|max:255',
            'colour' => 'required|string|max:255',
            'tax_status' => 'required|string|max:255',
            'tax_due_date' => 'string|max:255',
            'mot_status' => 'required|string|max:255',
            'mot_expiry_date' => 'required|string|max:255',
            'year_of_manufacture' => 'required|integer',
            'fuel_type' => 'required|string|max:255',
            'type_approval' => 'required|string|max:255',
            'date_of_last_v5_c_issued' => 'string|max:255',
            'wheelplan' => 'required|string|max:255',
        ]);

        try {
            // Create a new vehicle with the validated data
            $vehicle = Vehicle::create($validatedData);

            // Return a JSON response with the created vehicle and status code 201
            return response()->json($vehicle, 201);
        } catch (\Exception $e) {
            // Return a JSON response with the error message and status code 422 (Unprocessable Entity)
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
