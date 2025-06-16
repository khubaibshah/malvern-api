<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerSaleRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'email',
        'postcode',
        'phone',
        'vehicle_id',
    ];

    public function vehicle()
    {
        return $this->belongsTo(CustomerSaleRequestVehicle::class, 'vehicle_id');
    }
}
