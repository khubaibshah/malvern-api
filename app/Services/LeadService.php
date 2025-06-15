<?php

namespace App\Services;

use App\Mail\LeadEnquiryMail;
use App\Mail\ScheduleTestDriveMail;
use App\Models\Lead;
use Illuminate\Support\Facades\Mail;

class LeadService
{
    public function createLead(array $data): void
    {
        $lead = Lead::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'phone'      => $data['phone'] ?? null,
            'message'    => $data['message'],
            'vehicle_id' => $data['vehicle_id'] ?? null,
        ]);

        $lead->load('vehicle');

        Mail::to(config('mail.leads_to'))->send(new LeadEnquiryMail($lead));
    }

    public function scheduleTestDrive(array $data): void
    {
        $lead = Lead::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'phone'      => $data['phone'] ?? null,
            'vehicle_id' => $data['vehicle_id'] ?? null,
        ]);

        $lead->load('vehicle');

        Mail::to(config('mail.test_drives_to'))->send(new ScheduleTestDriveMail($lead));
    }
}
