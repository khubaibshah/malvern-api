<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\VehicleFactory;

class Vehicle extends Model
{
    use HasFactory;

    protected $table = 'vehicles';

    protected $fillable = [
        'id',
        'registration_number',
        'make',
        'colour',
        'tax_status',
        'tax_due_date',
        'mot_status',
        'mot_expiry_date',
        'year_of_manufacture',
        'fuel_type',
        'type_approval',
        'date_of_last_v5c_issued',
        'wheelplan',
    ];


}
