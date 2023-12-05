<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'bookings';

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'vehicle_make_model',
        'booking_datetime',
    ];
}