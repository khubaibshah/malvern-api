<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
        // Fetch all vehicles from the database
        $vehicles = Vehicle::all();

        // Return a JSON response with the fetched vehicles
        return response()->json($vehicles);
    }

    public function show($id)
    {
        // Fetch a single vehicle by its ID from the database
        $vehicle = Vehicle::findOrFail($id);

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
        'tax_due_date' => 'required|date',
        'mot_status' => 'required|string|max:255',
        'mot_expiry_date' => 'required|date',
        'year_of_manufacture' => 'required|string|max:255',
        'fuel_type' => 'required|string|max:255',
        'type_approval' => 'required|string|max:255',
        'date_of_last_v5c_issued' => 'required|date',
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
