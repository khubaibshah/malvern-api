<?php

namespace App\Http\Controllers;

use App\Mail\SellYourCarMail;
use App\Services\LeadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{

    public function lead(Request $request, LeadService $leadService)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string',
            'vehicle_id' => 'nullable|integer|exists:scs_cars,id',
        ]);

        try {
            $lead = $leadService->createLead($validated);

            return response()->json([
                'message' => 'Lead submitted successfully.',
                'lead' => $lead
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to submit lead.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function testDrive(Request $request, LeadService $leadService)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'vehicle_id' => 'required|integer|exists:scs_cars,id',
        ]);

        try {
            $testDrive = $leadService->scheduleTestDrive($validated);

            return response()->json([
                'message' => 'Test drive scheduled successfully.',
                'test_drive' => $testDrive
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to schedule test drive.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function sellYourCar(Request $request)
    {
        $validated = $request->validate([
            'fullName'     => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'postcode'     => 'required|string|max:20',
            'phone'        => 'nullable|string|max:20',
            'registration' => 'required|string|max:10',
            'vehicle'      => 'required|array',
        ]);

        // Send the email to your sales team
        Mail::to(config('mail.sell_car_to'))->send(new SellYourCarMail($validated));

        return response()->json(['message' => 'Sell request submitted successfully.']);
    }
}
