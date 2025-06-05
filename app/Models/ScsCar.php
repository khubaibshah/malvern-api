<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScsCar extends Model
{
    use HasFactory;

    protected $fillable = [
        'make', 'model', 'year', 'vrm', 'reg_date', 'registration_date','man_year',
        'variant', 'price', 'plus_vat', 'vat_qualifying', 'was_price', 'trade_price',
        'trade_text', 'price_above_40k', 'mileage', 'engine_cc', 'fuel_type',
        'body_style', 'colour', 'doors', 'gearbox', 'keys', 'veh_type', 'description', 'registration'
    ];
    
    public function images()
    {
        return $this->hasMany(ScsCarImage::class);
    }
}
