<?php
// app/Models/ScsCarImage.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScsCarImage extends Model
{
    use HasFactory;

    protected $fillable = ['car_image', 'scs_car_id'];

    // Accessor to get image as a base64 encoded string
    public function getCarImageAttribute($value)
    {
        return base64_encode($value);
    }

    public function car()
    {
        return $this->belongsTo(ScsCar::class);
    }
}
