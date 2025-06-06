<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'message',
        'vehicle_id',
    ];

    /**
     * Relationship to the vehicle (if applicable)
     */
    public function vehicle()
    {
        return $this->belongsTo(ScsCar::class, 'vehicle_id');
    }
}
