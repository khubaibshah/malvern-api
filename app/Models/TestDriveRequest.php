<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestDriveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'vehicle_id',
    ];

    public function vehicle()
    {
        return $this->belongsTo(ScsCar::class, 'vehicle_id');
    }
}
