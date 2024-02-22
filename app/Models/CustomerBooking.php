<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerBooking extends Model
{
    use HasFactory;

    protected $table = 'customer_bookings';

    protected $fillable = [
        'Booking_reference',
        'vehicle_make',
        'vehicle_model',
        'job_repair_id',
        'bookings_datetime',
        'customer_notes',
        'repair_price',
        'deposit_paid',
    ];

    public function jobCategory()
    {
        return $this->belongsTo(JobCategory::class, 'job_repair_id');
    }

}
