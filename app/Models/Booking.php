<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\BookingFactory;

class Booking extends Model
{
    use HasFactory;


    protected $table = 'bookings';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone_number',
        'vehicle_make_model',
        'notes',
        'booking_datetime',
    ];

    //seeding for test scenarios
    protected $factory = BookingFactory::class;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_booking_id');
    }
}
