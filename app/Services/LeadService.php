<?php

namespace App\Services;

use App\Mail\LeadEnquiryMail;
use App\Mail\ScheduleTestDriveMail;
use App\Mail\SellYourCarMail;
use App\Models\CustomerSaleRequest;
use App\Models\CustomerSaleRequestVehicle;
use App\Models\Lead;
use Illuminate\Support\Facades\Mail;

class LeadService
{
    /**
     * Create a new lead and send email notification.
     * @param array $data
     * @return Lead
     */
    public function createLead(array $data): void
    {
        $lead = Lead::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'phone'      => $data['phone'] ?? null,
            'message'    => $data['message'],
            'vehicle_id' => $data['vehicle_id'] ?? null,
            'source'    => 'Vehicle Enquiry Form',
        ]);

        $lead->load('vehicle');

        Mail::to(config('mail.leads_to'))->send(new LeadEnquiryMail($lead));
    }
    /**
     * Schedule a test drive and send email notification.
     * @param array $data
     * @return void
     */ 
    public function scheduleTestDrive(array $data): void
    {
        $lead = Lead::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'phone'      => $data['phone'] ?? null,
            'vehicle_id' => $data['vehicle_id'] ?? null,
            'source'    => 'Test Drive Form',
            'message'    => 'Test Drive',

        ]);

        $lead->load('vehicle');

        Mail::to(config('mail.test_drives_to'))->send(new ScheduleTestDriveMail($lead));
    }

    /**
     * Handle customer sale request and send email notification.
     * @param array $data
     * @return void
     */ 
    public function handleCustomerSaleRequest(array $data): void
    {
        $vehicle = CustomerSaleRequestVehicle::create([
            'registration'      => $data['vehicle']['registration'] ?? null,
            'make'              => $data['vehicle']['make'] ?? null,
            'model'             => $data['vehicle']['model'] ?? null,
            'primary_colour'    => $data['vehicle']['primaryColour'] ?? null,
            'fuel_type'         => $data['vehicle']['fuelType'] ?? null,
            'engine_size'       => $data['vehicle']['engineSize'] ?? null,
            'odometer_value'    => $data['vehicle']['odometerValue'] ?? null,
            'odometer_unit'     => $data['vehicle']['odometerUnit'] ?? null,
            'first_used_date'   => $data['vehicle']['firstUsedDate'] ?? null,
            'registration_date' => $data['vehicle']['registrationDate'] ?? null,
        ]);

        $saleRequest = CustomerSaleRequest::create([
            'full_name'  => $data['fullName'],
            'email'      => $data['email'],
            'postcode'   => $data['postcode'],
            'phone'      => $data['phone'] ?? null,
            'vehicle_id' => $vehicle->id,
        ]);

        Mail::to(config('mail.sell_car_to'))->send(new SellYourCarMail($saleRequest->load('vehicle')));
    }
}
