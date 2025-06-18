<?php

namespace App\Http\Controllers;

use App\Mail\SellYourCarMail;
use App\Models\Lead;
use App\Services\LeadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{

    public function __construct(protected LeadService $leadService)
    {
    }

    /**
     * Handle a lead enquiry submission.
     * @param Request $request
     * @param LeadService $leadService
     * @return \Illuminate\Http\JsonResponse
     */
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
            $lead = $this->leadService->createLead($validated);

            return response()->json([
                'message' => 'Enquiry submitted successfully.',
                'lead' => $lead
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to submit Enquiry.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle a test drive request submission.
     * @param Request $request 
     * @param LeadService $leadService
     * @return \Illuminate\Http\JsonResponse
     */
    public function testDrive(Request $request, LeadService $leadService)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'vehicle_id' => 'required|integer|exists:scs_cars,id',
        ]);

        try {
            $testDrive = $this->leadService->scheduleTestDrive($validated);

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

    /**
     *  Handle a customer vehicle sale request.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function customerVehicleSale(Request $request)
    {
        $validated = $request->validate([
            'fullName'     => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'postcode'     => 'required|string|max:20',
            'phone'        => 'nullable|string|max:20',
            'registration' => 'required|string|max:10',
            'vehicle'      => 'required|array',
            'partEx'      => 'nullable|boolean',
        ]);
         try {
            $this->leadService->handleCustomerSaleRequest($validated);

            return response()->json([
                'message' => 'Sell your car enquiry successful.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to submit sell your car enquiry.',
                'message' => $e->getMessage()
            ], 500);
        }
      
    }
}
