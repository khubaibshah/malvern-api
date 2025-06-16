<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerSaleRequestVehicle extends Model
{
    use HasFactory;

    protected $table = 'customer_sale_request_vehicles';
    
    protected $fillable = [
        'registration',
        'make',
        'model',
        'primary_colour',
        'fuel_type',
        'engine_size',
        'odometer_value',
        'odometer_unit',
        'first_used_date',
        'registration_date',
    ];

    public function saleRequest()
    {
        return $this->hasOne(CustomerSaleRequest::class, 'vehicle_id');
    }
}
