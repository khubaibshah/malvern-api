<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScsCar extends Model
{
    use HasFactory;

    protected $fillable = [
        'make', 'model', 'year', 'vrm', 'reg_date', 'man_year', 'reg_letter',
        'variant', 'price', 'plus_vat', 'vat_qualifying', 'was_price', 'trade_price',
        'trade_text', 'price_above_40k', 'mileage', 'engine_cc', 'fuel_type',
        'body_style', 'colour', 'doors', 'veh_type', 'veh_status', 'stock_id',
        'ebay_gt_title', 'subtitle', 'description'
    ];
    
    public function images()
    {
        return $this->hasMany(ScsCarImage::class);
    }
}
