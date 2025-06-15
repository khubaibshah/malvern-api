<?php

namespace App\Services;

use App\Mail\LeadEnquiryMail;
use App\Mail\ScheduleTestDriveMail;
use App\Models\Lead;
use Illuminate\Support\Facades\Mail;

class LeadService
{
    public function createLead(array $data): Lead
    {
        // Create the lead record in the database
        $lead = Lead::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'phone'      => $data['phone'] ?? null,
            'message'    => $data['message'],
            'vehicle_id' => $data['vehicle_id'] ?? null,
        ]);

        // Eager-load the related vehicle if needed for the email view
        $lead->load('vehicle');

        // Send the enquiry email
        Mail::to(config('mail.leads_to'))->send(new LeadEnquiryMail($lead));

        return $lead;
    }

    public function scheduleTestDrive(array $data): Lead
    {
        // Create the lead record for the test drive in the database
        $lead = Lead::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'phone'      => $data['phone'] ?? null,
            'vehicle_id' => $data['vehicle_id'] ?? null,
        ]);
        // Eager-load the related vehicle if needed for the email view
        $lead->load('vehicle');
        // Send the test drive email
        Mail::to(config('mail.test_drives_to'))->send(new ScheduleTestDriveMail($lead));
        return $lead;
    }

}
